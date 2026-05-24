<?php

namespace App\Libraries\OAuth\Repositories;

use App\Libraries\OAuth\Entities\UserEntity;
use OpenIDConnectServer\Repositories\IdentityProviderInterface;

class IdentityRepository implements IdentityProviderInterface
{
    /**
     * @param string $identifier The user's identifier
     * @return UserEntity
     */
    public function getUserEntityByIdentifier($identifier)
    {
        $db = \Config\Database::connect();
        $user = $db->table('master.users')->where('id', $identifier)->get()->getRow();

        if (!$user) {
            return null;
        }

        $claims = [
            'sub'               => (string) $user->id,
            'name'              => $user->full_name,
            'preferred_username' => $user->username,
            'role'              => $user->role,
        ];

        return new UserEntity((string) $identifier, $claims);
    }
}
