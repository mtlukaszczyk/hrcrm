<?php

namespace App\Model;

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as DB;

class Group extends Model {

    protected $table = 'group';

    public static function getMyGroups($userID) {

        return self::where('user_id', '=', $userID)
                        ->where('state', '=', 'on')
                        ->get(['id', 'name'])
                        ->toArray();
    }

    public static function getContactSelected($contactID) {

        return DB::table('contact_group_rel')
                        ->where('contact_id', '=', $contactID)
                        ->pluck('group_id')
                        ->toArray();
    }

    public static function saveContactGroups($contactID, $groups) {
        
        DB::table('contact_group_rel')
                        ->where('contact_id', '=', $contactID)
                        ->delete();
        
        if (count($groups) > 0) {
        
            foreach($groups as $group) {

                DB::table('contact_group_rel')
                        ->insert([
                            'contact_id' => $contactID,
                            'group_id' => $group
                        ]);
            }
        }
    }
}
