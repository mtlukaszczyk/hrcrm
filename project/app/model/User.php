<?php

namespace App\Model;

use \Illuminate\Database\Eloquent\Model as Model;

class User extends Model {

    protected $table = 'user';

    public static function getUserLoginData($email, $password) {

        $data = User::where('email', '=', $email)
                ->where('state', '=', 'on')
                ->get(['id', 'email', 'password']);

        if (isset($data[0])) {

            if (password_verify($password, $data[0]->password)) {
                return $data;
            }
        }

        return [];
    }

}
