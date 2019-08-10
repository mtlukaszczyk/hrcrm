<?php
namespace App\Model;

use \Illuminate\Database\Eloquent\Model as Model;

class Comment extends Model {

    protected $table = 'comment';
    
    public static function getMyComments($userID, $contactID = '', $type = '') {
        
        return self::where('user_id', '=', $userID)
                ->when($contactID !== '', function($query) use ($contactID) {
                    $query->where('contact_id', '=', $contactID);
                })
                ->when($type !== '', function($query) use ($type) {
                    $query->where('type', '=', $type);
                })
                ->where('state', '=', 'on')
                ->orderBy('created_at', 'desc')
                ->get(['id', 'contact_id', 'message', 'type', 'act_dt', 'important', 'created_at']);
            
    }

}
