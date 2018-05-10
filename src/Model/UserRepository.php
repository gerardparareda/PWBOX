<?php

namespace PwBox\Model;

interface UserRepository{
    public function save(User $user);
    public function lookFor(User $user);
}