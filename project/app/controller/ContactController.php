<?php

namespace App\Controller;

use \Illuminate\Database\Eloquent\ModelNotFoundException;
use \App\Model\Contact as ContactModel;
use \App\Model\Group as GroupModel;
use \App\Model\CustomField as CustomFieldModel;
use App\Helper\ContactsList as ContactsListHelper;

class Contact extends Controller {

    const FIELDS = ['name', 'surname', 'email', 'phone', 'linkedin', 'xing', 'www', 'city', 'greeting', 'contract_type', 'availability', 'salary'];

    public static function init() {
        \Engine\Assets::add('contact.js');
    }

    public static function index() {
        self::table();
    }

    public static function table() {

        \Engine\Assets::add("external/datatables/jquery.dataTables.min.js");
        \Engine\Assets::dataTableExport();

        $contacts = ContactModel::getMyContacts(self::$user->getID());
        $contactsListTemplate = ContactsListHelper::getMyContactsListTemplate(self::$user->getID());

        self::render('contact/list.twig', [
            'contacts' => $contacts,
            'template' => $contactsListTemplate
        ]);
    }

    public static function saveGroups($groupsSelected) {

        $contactID = \Engine\Session::get('contactID');

        GroupModel::saveContactGroups($contactID, $groupsSelected);

        self::render('json', [
            'state' => 'ok'
        ]);
    }

    public static function profile($id) {

        \Engine\Assets::add("external/bootstrap-select.min.js");
        \Engine\Assets::add("external/bootstrap-select.min.css");

        $groups = GroupModel::getMyGroups(self::$user->getID());
        $selected = GroupModel::getContactSelected($id);

        $customFields = CustomFieldModel::getMyFields(self::$user->getID(), false);

        foreach ($groups as &$group) {
            $group['selected'] = in_array($group['id'], $selected);
        }

        if ($id !== 'new') {

            if (self::isMyContact($id)) {

                try {
                    $contact = ContactModel::findOrFail($id);
                    \Engine\Session::set('contactID', $id);

                    $customFieldsValues = $contact->custom_fields;

                    if (!is_null($customFieldsValues) && $customFieldsValues !== '') {

                        $customFieldsValues = json_decode($customFieldsValues, true);

                        foreach ($customFieldsValues as $key => $value) {
                            foreach ($customFields as &$field) {
                                if ($field['id'] == $key) {
                                    $field['value'] = $value;
                                }
                            }
                        }
                    }

                    self::render('contact/profile.twig', [
                        'data' => $contact,
                        'groups' => $groups,
                        'customFields' => $customFields
                    ]);
                } catch (ModelNotFoundException $e) {
                    self::render("404.twig");
                }
            } else {
                self::render("404.twig");
            }
        } else {

            $newContactID = ContactModel::where('state', '=', 'new')
                    ->where('user_id', '=', self::$user->getID())
                    ->value('id');

            if (is_null($newContactID)) {
                $newContactID = ContactModel::insertGetId([
                            'user_id' => self::$user->getID(),
                            'state' => 'new'
                ]);
            }

            \Engine\Session::set('contactID', $newContactID);

            self::render('contact/profile.twig', [
                'groups' => $groups,
                'customFields' => $customFields
            ]);
        }
    }

    public static function delete($id) {

        ContactModel::where('user_id', '=', self::$user->getID())
                ->where('id', '=', $id)
                ->update(['state' => 'off']);

        \Engine\App::redirect('contact', 'table');
    }

    public static function editDetail($type, $value) {

        $contactID = \Engine\Session::get('contactID');

        if (self::isMyContact($contactID)) {
            self::makeActive($contactID);

            if (in_array($type, self::FIELDS)) {
                $contact = ContactModel::findOrFail($contactID);
                $contact->{$type} = $value;
                $result = $contact->save();

                self::render('json', ['result' => 'ok', 'data' => $result]);
            } else if (substr($type, 0, 6) == 'custom') {

                $fieldID = substr($type, 6);
                $contact = ContactModel::findOrFail($contactID);

                $customFields = json_decode($contact->custom_fields);
                $customFields[$fieldID] = $value;
                $customFields = json_encode($customFields);
                $contact->custom_fields = $customFields;
                $result = $contact->save();

                self::render('json', ['result' => 'ok', 'data' => $result]);
            }

            self::render('json', ['result' => 'err']);
        }

        self::render('json', ['result' => 'err']);
    }

    private static function isMyContact($id) {

        try {
            $contact = ContactModel::findOrFail($id);
            return ($contact->user_id == self::$user->getID());
        } catch (ModelNotFoundException $e) {
            return false;
        }
    }

    private static function makeActive($id) {

        $contact = ContactModel::findOrFail($id);
        $contact->state = 'on';
        $contact->save();
    }

}
