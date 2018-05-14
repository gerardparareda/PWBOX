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


    public function save(User $user)
    {
        $sql = "INSERT INTO user(username, email, password, created_at, updated_at) VALUES(:username, :email, :password, :birthDay, :birthMonth, :birthYear,:created_at, :updated_at)";
        $stmt = $this->database->prepare($sql);
        $stmt->bindValue("username", $user->getUsername(), 'string');
        $stmt->bindValue("email", $user->getEmail(), 'string');
        $stmt->bindValue("password", md5($user->getPassword()), 'string');
        $stmt->bindValue("birthDay", $user->getBirthDay(), 'int');
        $stmt->bindValue("birthMonth", $user->getBirthMonth(), 'string');
        $stmt->bindValue("birthYear", md5($user->getBirthYear()), 'int');
        $stmt->bindValue("created_at", $user->getCreatedAt()->format(self::DATE_FORMAT));
        $stmt->bindValue("updated_at", $user->getUpdatedAt()->format(self::DATE_FORMAT));
        $stmt->execute();
    }

    /**
     * @param User $user
     * @throws \Doctrine\DBAL\DBALException
     */
    public function lookFor(User $user)
    {
        $sql = "SELECT * FROM user WHERE email = :email AND password = :password";
        $stmt = $this->database->prepare($sql);
        $stmt->bindValue("email", $user->getEmail(), 'string');
        $stmt->bindValue("password", $user->getPassword(), 'string');
        $result = $stmt->execute();
        $user = $stmt->fetch();
        //Revisar
        var_dump($user);

        if ($user =! null) {
            return true;
        }
        return false;
    }
}