<?php

namespace App\Libraries\OAuth\Entities;

use League\OAuth2\Server\Entities\UserEntityInterface;
use OpenIDConnectServer\Entities\ClaimSetInterface;
use League\OAuth2\Server\Entities\Traits\EntityTrait;

class UserEntity implements UserEntityInterface, ClaimSetInterface
{
    use EntityTrait;

    protected $claims = [];

    public function __construct(string $identifier, array $claims = [])
    {
        $this->setIdentifier($identifier);
        $this->claims = $claims;
    }

    public function getClaims(): array
    {
        return $this->claims;
    }
}
