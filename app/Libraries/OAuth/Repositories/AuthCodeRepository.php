<?php

namespace App\Libraries\OAuth\Repositories;

use App\Libraries\OAuth\Entities\AuthCodeEntity;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;

class AuthCodeRepository implements AuthCodeRepositoryInterface
{
    public function getNewAuthCode(): AuthCodeEntity
    {
        return new AuthCodeEntity();
    }

    public function persistNewAuthCode(AuthCodeEntityInterface $authCodeEntity): void
    {
        $db = \Config\Database::connect();

        $scopes = array_map(function ($scope) {
            return $scope->getIdentifier();
        }, $authCodeEntity->getScopes());

        $db->table('oauth.auth_codes')->insert([
            'id' => $authCodeEntity->getIdentifier(),
            'client_identifier' => $authCodeEntity->getClient()->getIdentifier(),
            'user_id' => $authCodeEntity->getUserIdentifier(),
            'scopes' => json_encode($scopes),
            'revoked' => 0,
            'expires_at' => $authCodeEntity->getExpiryDateTime()->format('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function revokeAuthCode(string $codeId): void
    {
        $db = \Config\Database::connect();
        $db->table('oauth.auth_codes')->where('id', $codeId)->update(['revoked' => 1]);
    }

    public function isAuthCodeRevoked(string $codeId): bool
    {
        $db = \Config\Database::connect();
        $code = $db->table('oauth.auth_codes')->where('id', $codeId)->get()->getRow();

        if (!$code) {
            return true;
        }
        return (bool) $code->revoked;
    }
}
