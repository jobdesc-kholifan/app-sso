<?php

namespace App\Libraries\OAuth\Repositories;

use App\Libraries\OAuth\Entities\RefreshTokenEntity;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;

class RefreshTokenRepository implements RefreshTokenRepositoryInterface
{
    public function getNewRefreshToken(): RefreshTokenEntityInterface
    {
        return new RefreshTokenEntity();
    }

    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity): void
    {
        $db = \Config\Database::connect();
        $db->table('oauth.refresh_tokens')->insert([
            'id' => $refreshTokenEntity->getIdentifier(),
            'access_token_id' => $refreshTokenEntity->getAccessToken()->getIdentifier(),
            'revoked' => 0,
            'expires_at' => $refreshTokenEntity->getExpiryDateTime()->format('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function revokeRefreshToken($tokenId): void
    {
        $db = \Config\Database::connect();
        $db->table('oauth.refresh_tokens')->where('id', $tokenId)->update(['revoked' => 1]);
    }

    public function isRefreshTokenRevoked($tokenId): bool
    {
        $db = \Config\Database::connect();
        $token = $db->table('oauth.refresh_tokens')->where('id', $tokenId)->get()->getRow();

        if (!$token) {
            return true;
        }
        return (bool) $token->revoked;
    }
}
