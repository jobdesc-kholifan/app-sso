<?php

namespace App\Libraries\OAuth\Entities;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\Traits\ClientTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;

class ClientEntity implements ClientEntityInterface
{
    use EntityTrait, ClientTrait;

    public function __construct(string $identifier, string $name, $redirectUri, bool $isConfidential = false)
    {
        $this->setIdentifier($identifier);
        $this->name = $name;
        $this->redirectUri = $redirectUri;
        $this->isConfidential = $isConfidential;
    }
}
