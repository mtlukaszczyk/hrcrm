<?php

namespace App\Controller;

use \App\Model\Group as GroupModel;

class Groups extends Controller {

    public static function init() {
        \Engine\Assets::add('group.js');
    }

    public static function index() {
        self::render('group/index.twig');
    }

    public static function get() {
        self::render('json', [
            'data' => GroupModel::getMyGroups(self::$user->getID())
        ]);
    }

    public static function save($id, $name) {

        if ($id == '' || $id == 0) {
            $id = GroupModel::insertGetId([
                        'user_id' => self::$user->getID(),
                        'name' => $name,
                        'state' => 'on'
            ]);

            $res = ['data' => $id];
        } else {
            $result = GroupModel::where('id', '=', $id)
                    ->update([
                'name' => $name,
            ]);

            $res = ['data' => $result];
        }


        self::render('json', $res);
    }

    public static function delete($id) {
        if (self::isMyGroup($id)) {
            GroupModel::where('id', '=', $id)
                    ->update(['state' => 'off']);
        }
    }

    private static function isMyGroup($id) {

        try {
            $contact = GroupModel::findOrFail($id);
            return ($contact->user_id == self::$user->getID());
        } catch (ModelNotFoundException $e) {
            return false;
        }
    }

}
