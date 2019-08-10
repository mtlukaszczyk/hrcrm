<?php
namespace App\Model;

use \Illuminate\Database\Eloquent\Model as Model;

class Contact extends Model {

    protected $table = 'contact';
    
    public static function getMyContacts($userID) {
        
        return self::where('user_id', '=', $userID)
                ->where('state', '=', 'on')
                ->get(['id', 'name', 'surname', 'email', 'phone']);
            
    }

}