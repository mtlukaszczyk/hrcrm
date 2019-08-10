<?php

namespace App\Model;

use \Illuminate\Database\Eloquent\Model as Model;

class CustomField extends Model {

    protected $table = 'custom_field';

    public static function getMyFields($userID, $getHidden = true) {

        return self::where('user_id', '=', $userID)
                        ->where('state', '!=', 'off')
                        ->when(!$getHidden, function ($query) {
                            $query->where('state', '!=', 'hidden');
                        })
                        ->get(['id', 'name', 'state'])
                        ->toArray();
    }
}