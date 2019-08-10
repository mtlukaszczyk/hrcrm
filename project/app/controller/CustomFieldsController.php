<?php

namespace App\Controller;

use \App\Model\CustomField as CustomFieldModel;

class CustomFields extends Controller {

    public static function init() {
        \Engine\Assets::add('custom_field.js');
    }

    public static function index() {
        self::render('custom_field/index.twig');
    }

    public static function toggle($id, $act) {

        if (self::isMyField($id)) {
            CustomFieldModel::where('id', '=', $id)
                    ->update([
                        'state' => ($act == 'hide' ? 'hidden' : 'on')
            ]);

            self::render('json', [
                'res' => 'ok'
            ]);
        } else {
            self::render('json', [
                'res' => 'err'
            ]);
        }
    }

    public static function get() {
        self::render('json', [
            'data' => CustomFieldModel::getMyFields(self::$user->getID())
        ]);
    }

    public static function save($id, $name) {

        if ($id == '' || $id == 0) {
            $id = CustomFieldModel::insertGetId([
                        'user_id' => self::$user->getID(),
                        'name' => $name,
                        'state' => 'on'
            ]);

            $res = ['data' => $id];
        } else {
            $result = CustomFieldModel::where('id', '=', $id)
                    ->update([
                'name' => $name,
            ]);

            $res = ['data' => $result];
        }


        self::render('json', $res);
    }

    public static function delete($id) {
        if (self::isMyField($id)) {
            CustomFieldModel::where('id', '=', $id)
                    ->update(['state' => 'off']);
        }
    }

    private static function isMyField($id) {

        try {
            $contact = CustomFieldModel::findOrFail($id);
            return ($contact->user_id == self::$user->getID());
        } catch (ModelNotFoundException $e) {
            return false;
        }
    }

}
