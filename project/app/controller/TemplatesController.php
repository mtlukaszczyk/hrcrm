<?php

namespace App\Controller;

use \App\Model\Template as TemplateModel;

class Templates extends Controller {

    public static function init() {
        \Engine\Assets::add('template.js');
    }

    public static function index() {
        self::render('template/index.twig');
    }

    public static function get() {
        self::render('json', [
            'data' => TemplateModel::getMyTemplates(self::$user->getID())
        ]);
    }

    public static function save($id, $name, $content) {

        if ($id == '' || $id == 0) {
            $id = TemplateModel::insertGetId([
                        'user_id' => self::$user->getID(),
                        'name' => $name,
                        'content' => $content,
                        'state' => 'on'
            ]);

            $res = ['data' => $id];
        } else {
            $result = TemplateModel::where('id', '=', $id)
                    ->update([
                'content' => $content,
                'name' => $name,
            ]);

            $res = ['data' => $result];
        }


        self::render('json', $res);
    }

    public static function delete($id) {
        if (self::isMyTemplate($id)) {
            TemplateModel::where('id', '=', $id)
                    ->update(['state' => 'off']);
        }
    }

    private static function isMyTemplate($id) {

        try {
            $contact = TemplateModel::findOrFail($id);
            return ($contact->user_id == self::$user->getID());
        } catch (ModelNotFoundException $e) {
            return false;
        }
    }

}
