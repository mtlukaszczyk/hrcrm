<?php

namespace App\Controller;

use \App\Model\Settings as SettingsModel;
use \App\Helper\ContactsList as ContactsListHelper;

class Settings extends Controller {

    const OPTIONS = [
        'phone_protocol' => 'Phone protocol'
    ];

    public static function init() {
        \Engine\Assets::add('settings.js');
    }

    public static function index() {

        self::table();
    }

    public static function table() {

        $data = SettingsModel::where('user_id', '=', self::$user->getID())
                ->first(['phone_protocol'])
                ->toArray();

        self::render('settings/main.twig', [
            'fields' => self::OPTIONS,
            'data' => $data
        ]);
    }

    public static function contactsList() {

        \Engine\Assets::add('external/jquery-ui.js');

        $template = ContactsListHelper::getMyContactsListTemplate(self::$user->getID());

        $fullTemplate = [];

        foreach ($template as $key => $element) {

            $fullTemplate[] = [
                'key' => $key,
                'name' => $element,
                'selected' => true
            ];
        }

        foreach (ContactsListHelper::ALL_OPTIONS as $key => $element) {

            if (!in_array($element, $template)) {
                $fullTemplate[] = [
                    'key' => $key,
                    'name' => $element,
                    'selected' => false
                ];
            }
        }

        self::render('settings/contacts.twig', [
            'template' => $fullTemplate
        ]);
    }

    public static function saveContactsListTemplate($template) {
        
        foreach($template as &$templateElement) {
            if (!in_array($templateElement, array_keys(ContactsListHelper::ALL_OPTIONS))) {
                unset($templateElement);
            }
        }
        
        $template = implode(', ', $template);

        try {
            $comment = SettingsModel::findOrFail(self::$user->getID());
            $comment->contacts_list_template = $template;

            self::render('json', [
                'result' => $comment->save()
            ]);
        } catch (ModelNotFoundException $e) {
            self::render('json', [
                'result' => false
            ]);
        }
    }

    public static function save($type, $value) {

        if (in_array($type, array_keys(self::OPTIONS))) {
            $setting = SettingsModel::findOrFail(self::$user->getID());
            $setting->{$type} = $value;
            $result = $setting->save();

            self::render('json', ['result' => 'ok', 'data' => $result]);
        } else {

            self::render('json', ['result' => 'err']);
        }
    }

}
