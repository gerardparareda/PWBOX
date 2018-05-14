<?php

namespace PwBox\Model;

class User{
    private $id;
    private $username;
    private $email;
    private $password;
    private $birthDay;
    private $birthMonth;
    private $birthYear;
    private $createdAt;
    private $updatedAt;

    /**
     * User constructor.
     * @param $id
     * @param $username
     * @param $email
     * @param $password
     * @param $birthDay
     * @param $birthMonth
     * @param $birthYear
     * @param $createdAt
     * @param $updatedAt
     */
    public function __construct($id, $username, $email, $password, $birthDay, $birthMonth, $birthYear, $createdAt, $updatedAt)
    {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->birthDay = $birthDay;
        $this->birthMonth = $birthMonth;
        $this->birthYear = $birthYear;
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
    public function getBirthDay()
    {
        return $this->birthDay;
    }

    /**
     * @return mixed
     */
    public function getBirthMonth()
    {
        return $this->birthMonth;
    }

    /**
     * @return mixed
     */
    public function getBirthYear()
    {
        return $this->birthYear;
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