<?php

namespace App\Libraries\OAuth\Repositories;

use App\Libraries\OAuth\Entities\AccessTokenEntity;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;

class AccessTokenRepository implements AccessTokenRepositoryInterface
{
    public function getNewToken(
        ClientEntityInterface $clientEntity,
        array $scopes,
        string|null $userIdentifier = null
    ): AccessTokenEntityInterface {
        $accessToken = new AccessTokenEntity();
        $accessToken->setClient($clientEntity);
        foreach ($scopes as $scope) {
            $accessToken->addScope($scope);
        }
        $accessToken->setUserIdentifier($userIdentifier);

        return $accessToken;
    }

    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity): void
    {
        $db = \Config\Database::connect();

        $scopes = array_map(function ($scope) {
            return $scope->getIdentifier();
        }, $accessTokenEntity->getScopes());

        $db->table('oauth.access_tokens')->insert([
            'id' => $accessTokenEntity->getIdentifier(),
            'client_identifier' => $accessTokenEntity->getClient()->getIdentifier(),
            'user_id' => $accessTokenEntity->getUserIdentifier(),
            'scopes' => json_encode($scopes),
            'revoked' => 0,
            'expires_at' => $accessTokenEntity->getExpiryDateTime()->format('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function revokeAccessToken($tokenId): void
    {
        $db = \Config\Database::connect();
        $db->table('oauth.access_tokens')->where('id', $tokenId)->update(['revoked' => 1]);
    }

    public function isAccessTokenRevoked($tokenId): bool
    {
        $db = \Config\Database::connect();
        $token = $db->table('oauth.access_tokens')->where('id', $tokenId)->get()->getRow();

        if (!$token) {
            return true;
        }
        return (bool) $token->revoked;
    }
}
