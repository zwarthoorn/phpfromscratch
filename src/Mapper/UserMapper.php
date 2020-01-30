<?php


namespace PhpOpdracht\Mapper;


use PhpOpdracht\Maps\User;

class UserMapper
{

    public static function mapUser($databaseUser)
    {
        if ($databaseUser === false)
        {
            return null;
        }

        $user = new User();
        $user->setId($databaseUser['id']);
        $user->setFirstName($databaseUser['firstname']);
        $user->setLastName($databaseUser['lastname']);
        $user->setEmail($databaseUser['email']);
        $user->setSecretMessage($databaseUser['secret']);
        $user->setPassword($databaseUser['password']);
        $user->setAdmin($databaseUser['admin']);
        $user->setDeleted($databaseUser['deleted']);

        return $user;

    }
}