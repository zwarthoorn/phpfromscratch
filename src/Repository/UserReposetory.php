<?php


namespace PhpOpdracht\Repository;


use http\Exception\RuntimeException;
use mysql_xdevapi\Exception;
use PhpOpdracht\Config\DbConfig;
use PhpOpdracht\Mapper\UserMapper;
use PhpOpdracht\Maps\User;

class UserReposetory
{

    public function saveUser(User $user)
    {

        try {
            $connection = new \PDO(DbConfig::DNS, DbConfig::USER, DbConfig::PASS);
        } catch (\PDOException $e) {
            throw new RuntimeException('no database connection');
        }


        $statement = $connection->prepare(
            'INSERT INTO users (firstname,lastname,email,secret,password)
                    VALUES (:firstname, :lastname, :email, :secret, :password)'
        );


        $statement->bindParam('firstname', $user->getFirstName());
        $statement->bindParam('lastname', $user->getLastName());
        $statement->bindParam('email', $user->getEmail());
        $statement->bindParam('secret', $user->getSecretMessage());
        $statement->bindParam('password', $user->getPassword());

        $outcome = $statement->execute();

        if ($outcome === true)
        {
            return $connection->lastInsertId();
        }

        return false;
    }


    public function find(string $column, string $value): ?User
    {
        if ($value === '')
        {
            return null;
        }
        try {
            $connection = new \PDO(DbConfig::DNS, DbConfig::USER, DbConfig::PASS);
        } catch (\PDOException $e) {
            throw new RuntimeException('no database connection');
        }


        $statement = $connection->prepare(
            'SELECT * FROM users WHERE '.$column.' = :searcheble'
        );

        $statement->bindParam('searcheble', $value);

        $statement->execute();

        return UserMapper::mapUser($statement->fetch());
    }

    public function findAll($column = null, $value = null)
    {
        try {
            $connection = new \PDO(DbConfig::DNS, DbConfig::USER, DbConfig::PASS);
        } catch (\PDOException $e) {
            throw new RuntimeException('no database connection');
        }


        $stm = 'SELECT * FROM users ';
        if ($value !== null)
        {
            $stm = $stm . 'WHERE '.$column.' = :searcheble';
        }

        $statement = $connection->prepare(
            $stm
        );

        $statement->bindParam('searcheble', $value);

        $statement->execute();

       $userArray = [];

       foreach ($statement->fetchAll() as $user)
       {
           $userArray[] = UserMapper::mapUser($user);
       }

        return $userArray;
    }

    public function findAllLike($column, $value)
    {
        try {
            $connection = new \PDO(DbConfig::DNS, DbConfig::USER, DbConfig::PASS);
        } catch (\PDOException $e) {
            throw new RuntimeException('no database connection');
        }


        $stm = 'SELECT * FROM users ';
        if ($value !== null)
        {
            $value = '%'.$value.'%';
            $stm = $stm . 'WHERE '.$column.' LIKE :searcheble';
        }

        $statement = $connection->prepare(
            $stm
        );

        $statement->bindParam('searcheble', $value);

        $statement->execute();

        $userArray = [];

        foreach ($statement->fetchAll() as $user)
        {
            $userArray[] = UserMapper::mapUser($user);
        }

        return $userArray;
    }

    public function updateUser(User $user, $newPassword)
    {
        try {
            $connection = new \PDO(DbConfig::DNS, DbConfig::USER, DbConfig::PASS);
        } catch (\PDOException $e) {
            throw new RuntimeException('no database connection');
        }



        $statement = $connection->prepare(
            'UPDATE users SET firstname=:firstname, lastname=:lastname, email=:email, secret=:secret, password=:password
                       WHERE id=:id'
        );

        if ($newPassword)
        {
            $password = password_hash($user->getPassword(), PASSWORD_DEFAULT);
            $user->setPassword($password);
        }

        $statement->bindParam('firstname', $user->getFirstName());
        $statement->bindParam('lastname', $user->getLastName());
        $statement->bindParam('email', $user->getEmail());
        $statement->bindParam('secret', $user->getSecretMessage());
        $statement->bindParam('password', $user->getPassword());
        $statement->bindParam('id', $user->getId());


        $outcome = $statement->execute();

        return $outcome;
    }

    public function toggleAdmin($id)
    {
        try {
            $connection = new \PDO(DbConfig::DNS, DbConfig::USER, DbConfig::PASS);
        } catch (\PDOException $e) {
            throw new RuntimeException('no database connection');
        }


        $statement = $connection->prepare(
            'UPDATE users SET admin=:admin
                       WHERE id=:id'
        );

        $user = $this->find('id',(int) $id);

        $admin = 0;
        if ($user->getAdmin() === 0)
        {
            $admin = 1;
        }

        $statement->bindParam('admin', $admin);
        $statement->bindParam('id', $id);

        return $statement->execute();
    }

    public function toggleDeleted($id)
    {
        try {
            $connection = new \PDO(DbConfig::DNS, DbConfig::USER, DbConfig::PASS);
        } catch (\PDOException $e) {
            throw new RuntimeException('no database connection');
        }


        $statement = $connection->prepare(
            'UPDATE users SET deleted=:admin
                       WHERE id=:id'
        );

        $user = $this->find('id',(int) $id);

        $admin = 0;
        if ($user->getDeleted() === 0)
        {
            $admin = 1;
        }

        $statement->bindParam('admin', $admin);
        $statement->bindParam('id', $id);

        return $statement->execute();
    }

}