<?php
namespace Engine;

class User {

    private $id;
    private $email;

    public function __construct($user) {
        $this->id = $user['id'];
        $this->email = $user['email'];
    }

    public function getID() {
        if (isset($this->id)) {
            return $this->id;
        } else {
            return null;
        }
    }

    public function getEmail() {
        return $this->email;
    }

}