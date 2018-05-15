<?php

namespace PwBox\Model\Implementation;

use PwBox\Model\User;
use PwBox\Model\UserRepository;
use Doctrine\DBAL\Connection;

class DoctrineUserRepository implements UserRepository{

    private const DATE_FORMAT = 'Y-m-d H:i:s';

    private $database;

    /**
     * DoctrineUserRepository constructor.
     * @param $database
     */
    public function __construct(Connection $database)
    {
        //No cal connectar-se a la base de dades, perque ho fa sol amb els parametres
        // de app/settings.php
        $this->database = $database;
    }


    /**
     * @param User $user
     * @throws \Doctrine\DBAL\DBALException
     */
    public function save(User $user)
    {
        $sql = "INSERT INTO User(id, username, email, pass, birthDay, birthMonth, birthYear, created_at, updated_at) VALUES(null, :username, :email, :password, :birthDay, :birthMonth, :birthYear, :created_at, :updated_at)";
        $stmt = $this->database->prepare($sql);
        $stmt->bindValue("username", $user->getUsername(), 'string');
        $stmt->bindValue("email", $user->getEmail(), 'string');
        $stmt->bindValue("password", md5($user->getPassword()), 'string');
        $stmt->bindValue("birthDay", $user->getBirthDay(), 'integer');
        $stmt->bindValue("birthMonth", $user->getBirthMonth(), 'string');
        $stmt->bindValue("birthYear", $user->getBirthYear(), 'integer');
        $stmt->bindValue("created_at", $user->getCreatedAt()->format(self::DATE_FORMAT));
        $stmt->bindValue("updated_at", $user->getUpdatedAt()->format(self::DATE_FORMAT));
        $stmt->execute();

        //Ara retornem l'id de l'ususari.
        $sql = "SELECT * FROM User WHERE email = :email AND pass = :password";
        $stmt = $this->database->prepare($sql);
        $stmt->bindValue("email", $user->getEmail(), 'string');
        $stmt->bindValue("password", md5($user->getPassword()), 'string');
        $result = $stmt->execute();
        $newUser = $stmt->fetchColumn (0);

        return $newUser;
    }

    /**
     * @param User $user
     * @throws \Doctrine\DBAL\DBALException
     */
    public function lookFor(User $user)
    {
        $sql = "SELECT * FROM User WHERE email = :email AND pass = :password";
        $stmt = $this->database->prepare($sql);
        $stmt->bindValue("email", $user->getEmail(), 'string');
        $stmt->bindValue("password", md5($user->getPassword()), 'string');
        $result = $stmt->execute();
        $newUser = $stmt->fetch();
        //Revisar
        //var_dump($newUser);

        if ($newUser != null) {
            return true;
        }
        return false;
    }

    /**
     * @param User $user
     * @return bool
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getUserId(User $user)
    {
        $sql = "SELECT * FROM User WHERE email = :email AND pass = :password";
        $stmt = $this->database->prepare($sql);
        $stmt->bindValue("email", $user->getEmail(), 'string');
        $stmt->bindValue("password", md5($user->getPassword()), 'string');
        $result = $stmt->execute();
        $newUser = $stmt->fetchColumn (0);

        return $newUser;
    }
}