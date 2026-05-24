<?php

namespace App\Libraries\OAuth\Repositories;

use App\Libraries\OAuth\Entities\ClientEntity;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;

class ClientRepository implements ClientRepositoryInterface
{
    public function getClientEntity($clientIdentifier): ?ClientEntity
    {
        $db = \Config\Database::connect();
        $client = $db->table('oauth.clients')->where('client_identifier', $clientIdentifier)->get()->getRow();

        if (!$client) {
            return null;
        }

        return new ClientEntity(
            $clientIdentifier,
            $client->name,
            $client->redirect_uri,
            (bool) $client->is_confidential
        );
    }

    public function validateClient($clientIdentifier, $clientSecret, $grantType): bool
    {
        $db = \Config\Database::connect();
        $client = $db->table('oauth.clients')->where('client_identifier', $clientIdentifier)->get()->getRow();

        if (!$client) {
            return false;
        }

        if ($client->is_confidential && $clientSecret !== null) {
            return password_verify($clientSecret, $client->client_secret);
        }

        return !$client->is_confidential;
    }
}
