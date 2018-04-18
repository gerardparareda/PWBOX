<?php

namespace PwBox\Model;

class User{
    private $id;
    private $username;
    private $email;
    private $password;
    private $createdAt;
    private $updatedAt;

    /**
     * User constructor.
     * @param $id
     * @param $username
     * @param $email
     * @param $password
     * @param $createdAt
     * @param $updatedAt
     */
    public function __construct($id, $username, $email, $password, $createdAt, $updatedAt)
    {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }


}