<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use Config\Services;
use App\Models\UserModel;

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\ResourceServer;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\Exception\OAuthServerException;

use OpenIDConnectServer\IdTokenResponse;
use OpenIDConnectServer\ClaimExtractor;

use App\Libraries\OAuth\Repositories\ClientRepository;
use App\Libraries\OAuth\Repositories\AccessTokenRepository;
use App\Libraries\OAuth\Repositories\ScopeRepository;
use App\Libraries\OAuth\Repositories\AuthCodeRepository;
use App\Libraries\OAuth\Repositories\RefreshTokenRepository;
use App\Libraries\OAuth\Repositories\IdentityRepository;
use App\Libraries\OAuth\Entities\UserEntity;

use Nyholm\Psr7\ServerRequest;
use Nyholm\Psr7\Response as Psr7Response;
use Nyholm\Psr7\Factory\Psr17Factory;
use App\Libraries\OAuth\Exceptions\UserNotFoundException;

class OAuthController extends BaseController
{
    /** @var AuthorizationServer */
    protected $server;

    /** @var ResourceServer */
    protected $resourceServer;

    protected $identityRepository;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $clientRepository = new ClientRepository();
        $accessTokenRepository = new AccessTokenRepository();
        $scopeRepository = new ScopeRepository();
        $authCodeRepository = new AuthCodeRepository();
        $refreshTokenRepository = new RefreshTokenRepository();
        $this->identityRepository = new IdentityRepository();

        $privateKey = new CryptKey(WRITEPATH . 'keys/oauth-private.key', null, false);
        $publicKey = new CryptKey(WRITEPATH . 'keys/oauth-public.key', null, false);
        $encryptionKey = 'VRZpwZeqWotsKhXQn258caRfWJA1wnsBy9v8DkgnizQ=';

        $claimExtractor = new ClaimExtractor();
        $idTokenResponse = new IdTokenResponse($this->identityRepository, $claimExtractor);

        $this->server = new AuthorizationServer(
            $clientRepository,
            $accessTokenRepository,
            $scopeRepository,
            $privateKey,
            $encryptionKey,
            $idTokenResponse
        );

        $grant = new AuthCodeGrant(
            $authCodeRepository,
            $refreshTokenRepository,
            new \DateInterval('PT10M')
        );
        $grant->setRefreshTokenTTL(new \DateInterval('P1M'));

        $this->server->enableGrantType(
            $grant,
            new \DateInterval('PT1H')
        );

        $this->resourceServer = new ResourceServer(
            $accessTokenRepository,
            $publicKey
        );
    }

    private function createPsr7Request()
    {
        $psr17Factory = new Psr17Factory();
        $method = $this->request->getMethod();
        $uri = (string) $this->request->getUri();

        $psrRequest = $psr17Factory->createServerRequest($method, $uri)
            ->withParsedBody($this->request->getPost())
            ->withQueryParams($this->request->getGet());

        foreach ($this->request->headers() as $name => $header) {
            $psrRequest = $psrRequest->withHeader($name, $header->getValue());
        }

        return $psrRequest;
    }

    private function convertPsr7Response(Psr7Response $psrResponse)
    {
        $this->response->setStatusCode($psrResponse->getStatusCode());
        foreach ($psrResponse->getHeaders() as $name => $values) {
            $val = implode(', ', $values);
            if (strtolower($name) === 'content-type') {
                $this->response->setContentType($val);
            }
            $this->response->setHeader($name, $val);
        }
        $this->response->setBody((string) $psrResponse->getBody());
        return $this->response;
    }

    public function login()
    {
        $redirect = $this->request->getGet('redirect');
        if ($redirect) {
            session()->set('oauth_redirect_after_login', $redirect);
        }

        if (session()->get('isLoggedIn')) {
            $redirectUrl = session()->get('oauth_redirect_after_login') ?? '/master/users';
            session()->remove('oauth_redirect_after_login');
            return redirect()->to($redirectUrl);
        }

        return view('auth/v_oauth_login');
    }

    public function processLogin()
    {
        $throttler = Services::throttler();

        if ($throttler->check($this->request->getIPAddress(), 5, MINUTE) === false) {
            return redirect()->back()->with('error', 'Too many login attempts. Please try again later.');
        }

        $rules = [
            'email'    => 'required|min_length[3]',
            'password' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('error', 'Please fill all required fields correctly.')->withInput();
        }

        $username = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $userModel = new UserModel();
        $user = $userModel->where('username', $username)->first();

        if ($user) {
            if ((int) $user->status !== 1) {
                return redirect()->back()->with('error', 'Your account is inactive.');
            }

            if (password_verify($password, $user->user_password)) {
                $sessionData = [
                    'user_id'   => $user->id,
                    'username'  => $user->username,
                    'full_name' => $user->full_name,
                    'role'      => $user->role,
                    'isLoggedIn' => true
                ];

                $userModel->update($user->id, ['last_login' => date('Y-m-d H:i:s')]);
                session()->set($sessionData);

                $redirectUrl = session()->get('oauth_redirect_after_login') ?? '/master/users';
                session()->remove('oauth_redirect_after_login');
                return redirect()->to($redirectUrl);
            } else {
                return redirect()->back()->with('error', 'Invalid password.');
            }
        } else {
            return redirect()->back()->with('error', 'User not found.');
        }
    }

    public function logout()
    {
        // Hapus sesi pengguna di server pusat
        session()->destroy();

        // Cek jika ada URL untuk dikembalikan ke klien (post_logout_redirect_uri)
        $postLogoutUri = $this->request->getGet('post_logout_redirect_uri');

        if ($postLogoutUri) {
            // Validasi URL demi keamanan (opsional tapi disarankan)
            if (filter_var($postLogoutUri, FILTER_VALIDATE_URL)) {
                return redirect()->to($postLogoutUri);
            }
        }

        // Jika tidak ada, kembalikan ke layar login SSO biasa
        return redirect()->to('/oauth/login')->with('success', 'You have been logged out.');
    }

    public function authorize()
    {
        $psrRequest = $this->createPsr7Request();
        $psrResponse = new Psr7Response();

        try {
            $authRequest = $this->server->validateAuthorizationRequest($psrRequest);

            if (!session()->get('isLoggedIn')) {
                $currentUrl = current_url(true)->setQuery(http_build_query($this->request->getGet()));
                return redirect()->to('/oauth/login?redirect=' . urlencode((string)$currentUrl));
            }

            $userId       = (string) session()->get('user_id');
            $clientId     = $authRequest->getClient()->getIdentifier();

            $userEntity = new UserEntity($userId);
            $authRequest->setUser($userEntity);

            // Cek apakah user sudah pernah authorize client ini sebelumnya
            // (ada token yang belum di-revoke dan belum expired)
            $db = db_connect();
            $existingGrant = $db->table('oauth.access_tokens')
                ->where('user_id', $userId)
                ->where('client_identifier', $clientId)
                ->where('revoked', 0)
                ->where('expires_at >', date('Y-m-d H:i:s'))
                ->limit(1)
                ->get()
                ->getRow();

            if ($existingGrant) {
                // Sudah pernah authorize → langsung approve tanpa tampilkan consent screen
                $authRequest->setAuthorizationApproved(true);
                $psrResponse = $this->server->completeAuthorizationRequest($authRequest, $psrResponse);
                return $this->convertPsr7Response($psrResponse);
            }

            // Belum pernah authorize → tampilkan consent screen
            session()->set('oauth_auth_request', serialize($authRequest));

            $clientName = $authRequest->getClient()->getName();
            $scopes = array_map(function ($scope) {
                return $scope->getIdentifier();
            }, $authRequest->getScopes());

            return view('auth/v_oauth_consent', [
                'clientName' => $clientName,
                'scopes'     => $scopes
            ]);
        } catch (OAuthServerException $exception) {
            return $this->convertPsr7Response($exception->generateHttpResponse($psrResponse));
        } catch (\Exception $exception) {
            log_message('critical', '[SSO Authorization] ' . $exception->getTraceAsString());
            $psrResponse = $psrResponse->withStatus(500);
            $psrResponse->getBody()->write($exception->getMessage());
            return $this->convertPsr7Response($psrResponse);
        }
    }

    public function authorizeProcess()
    {
        $psrResponse = new Psr7Response();
        $authRequestObj = session()->get('oauth_auth_request');

        if (!$authRequestObj) {
            return redirect()->to('/login')->with('error', 'Authorization request expired.');
        }

        $authRequest = unserialize($authRequestObj);
        session()->remove('oauth_auth_request');

        $isApproved = $this->request->getPost('approve') === '1';
        $authRequest->setAuthorizationApproved($isApproved);

        try {
            $psrResponse = $this->server->completeAuthorizationRequest($authRequest, $psrResponse);
            return $this->convertPsr7Response($psrResponse);
        } catch (OAuthServerException $exception) {
            return $this->convertPsr7Response($exception->generateHttpResponse($psrResponse));
        } catch (\Exception $exception) {
            log_message('critical', '[SSO Authorization Process]: ' . $exception->getMessage() . ' Trace ' . $exception->getTraceAsString());
            $psrResponse = $psrResponse->withStatus(500);
            $psrResponse->getBody()->write($exception->getMessage());
            return $this->convertPsr7Response($psrResponse);
        }
    }

    public function token()
    {
        $psrRequest = $this->createPsr7Request();
        $psrResponse = new Psr7Response();

        try {
            $psrResponse = $this->server->respondToAccessTokenRequest($psrRequest, $psrResponse);
            return $this->convertPsr7Response($psrResponse);
        } catch (OAuthServerException $exception) {
            return $this->convertPsr7Response($exception->generateHttpResponse($psrResponse));
        } catch (\Exception $exception) {
            log_message('critical', '[SSO Token]: ' . $exception->getMessage() . ' Trace ' . $exception->getTraceAsString());
            $psrResponse = $psrResponse
                ->withStatus(500)
                ->withHeader('Content-Type', 'application/json');
            $psrResponse->getBody()->write(json_encode([
                'error'             => 'server_error',
                'error_description' => $exception->getMessage()
            ]));
            return $this->convertPsr7Response($psrResponse);
        }
    }

    public function userinfo()
    {
        $psrRequest = $this->createPsr7Request();
        $psrResponse = new Psr7Response();

        try {
            $psrRequest = $this->resourceServer->validateAuthenticatedRequest($psrRequest);
            $userId = $psrRequest->getAttribute('oauth_user_id');

            $userEntity = $this->identityRepository->getUserEntityByIdentifier($userId);

            if (!$userEntity) {
                return $this->response->setJSON(['error' => 'User not found'])->setStatusCode(404);
            }

            return $this->response->setJSON($userEntity->getClaims());
        } catch (OAuthServerException $exception) {
            return $this->convertPsr7Response($exception->generateHttpResponse($psrResponse));
        } catch (\Exception $exception) {
            log_message('critical', '[SSO UserInfo]: ' . $exception->getMessage() . ' Trace ' . $exception->getTraceAsString());
            $psrResponse = $psrResponse
                ->withStatus(500)
                ->withHeader('Content-Type', 'application/json');
            $psrResponse->getBody()->write(json_encode([
                'error'             => 'server_error',
                'error_description' => $exception->getMessage()
            ]));
            return $this->convertPsr7Response($psrResponse);
        }
    }

    public function tutorial()
    {
        return view('auth/v_oauth_tutorial');
    }
}
