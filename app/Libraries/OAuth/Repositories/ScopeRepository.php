<?php

namespace App\Libraries\OAuth\Repositories;

use App\Libraries\OAuth\Entities\ScopeEntity;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;

class ScopeRepository implements ScopeRepositoryInterface
{
    public function getScopeEntityByIdentifier($identifier): ?ScopeEntity
    {
        $allowedScopes = [
            'openid',
            'profile',
            'email',
        ];

        if (in_array($identifier, $allowedScopes)) {
            return new ScopeEntity($identifier);
        }

        return null;
    }

    public function finalizeScopes(
        array $scopes,
        string $grantType,
        ClientEntityInterface $clientEntity,
        string|null $userIdentifier = null,
        ?string $authCodeId = null
    ): array {
        return $scopes;
    }
}
