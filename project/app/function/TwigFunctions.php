<?php

$twigFunctions['simpleInput'] = new \Twig_Function('simpleInput', function($id, $value, $label = '', $dataGrab = true, $classes = []) {

    $o = '<p class="form-group has-feedback">
        <label>' . $label . '</label>
        <input type="text" class="form-control ' . ($dataGrab ? 'data-grab ' : '') . implode(', ', $classes) . '" id="' . $id . '" value="' . $value . '" old_value="' . $value . '"/>
     </p>';

    return $o;
});

$twigFunctions['simpleText'] = new \Twig_Function('simpleText', function($id, $value, $label = '', $dataGrab = true, $classes = []) {

    $o = '<div class="form-group">
        <label>' . $label . '</label>
        <textarea class="form-control ' . ($dataGrab ? 'data-grab ' : '') . implode(', ', $classes) . '" id="' . $id . '">' . $value .'</textarea>
     </div>';

    return $o;
});


$twigFunctions['settings'] = new \Twig_Function('settings', function($name) {

    $userID = \App\Controller\Controller::$user->getID();
    return \App\Model\Settings::getValue($name, $userID);
});