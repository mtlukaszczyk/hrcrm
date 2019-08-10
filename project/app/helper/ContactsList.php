<?php

namespace App\Helper;

use \App\Model\Settings as SettingsModel;

class ContactsList {

    const DEFAULT_TEMPLATE = ['name' => 'Name', 'surname' => 'Surname', 'email' => 'E-mail', 'phone' => 'Phone'];
    const ALL_OPTIONS = [
        'name' => 'Name', 
        'surname' => 'Surname', 
        'email' => 'E-mail', 
        'phone' => 'Phone',
        'linkedin' => 'LinkedIN', 
        'xing' => 'XING', 
        'www' => 'Website', 
        'city' => 'City', 
        'greeting' => 'Greeting', 
        'contract_type' => 'Prefered contract type', 
        'availability' => 'Availability', 
        'salary' => 'Prefered salary'
    ];
    
    private static $template = [];

    public static function getMyContactsListTemplate($userID) {

        if (self::$template == []) {

            $template = SettingsModel::getValue('contacts_list_template', $userID);

            if (is_null($template) || $template == '') {
                self::$template = self::DEFAULT_TEMPLATE;
            } else {
                
                $options = explode(', ', $template);
                foreach($options as $option) {
                    self::$template[$option] = self::ALL_OPTIONS[$option];
                }
            }
        }

        return self::$template;
    }

}
