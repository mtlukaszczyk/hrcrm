<?php

namespace App\Controller;
use \App\Model\Comment as CommentModel;

class Calendar extends Controller {

    public static function index() {
        
        $calendarComments = CommentModel::getMyComments(self::$user->getID(), '', 'termin');
        
        $translated = [];
        
        foreach($calendarComments as $comment) {
            $translated[] = [
                'id' => $comment->contact_id,
                'title' => $comment->message,
                'start' => str_replace(' ', 'T', $comment->act_dt),
                'url' => base_url . 'app/contact/profile/' . $comment->contact_id . '/'
            ];
        }

        \Engine\Assets::add("external/fullcalendar/fullcalendar.css");
        \Engine\Assets::add("external/fullcalendar/lib/moment.min.js");
        \Engine\Assets::add("external/fullcalendar/fullcalendar.js");

        self::render("calendar/index.twig", [
            'events' => $translated
        ]);
    }

}
