<?php
namespace App\Model;

use \Illuminate\Database\Eloquent\Model as Model;

class Settings extends Model {

    protected $table = 'settings';
    protected $primaryKey = 'user_id';
    
    public static function getValue($name, $userID) {
        
        return self::where('user_id', '=', $userID)
                ->value($name);
        
    }

}