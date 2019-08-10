<?php

namespace App\Model;

use \Illuminate\Database\Eloquent\Model as Model;

class Template extends Model {

    protected $table = 'template';

    public static function getMyTemplates($userID) {

        return self::where('user_id', '=', $userID)
                        ->where('state', '=', 'on')
                        ->get(['id', 'name', 'content']);
    }

}
