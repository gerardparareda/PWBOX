<?php

namespace PwBox\Model;

interface UserRepository{
    public function save(User $user);
    public function lookFor(User $user);
    public function getUserId(User $user);
    public function getEmailById(int $idUsuari);
    public function getUsernameById(int $idUsuari);
}