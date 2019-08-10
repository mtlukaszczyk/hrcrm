<?php

namespace App\Controller;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Carbon;
use \App\Model\Comment as CommentModel;

class Comments extends Controller {

    public static function get() {

        $contactID = \Engine\Session::get('contactID');
        $comments = CommentModel::getMyComments(self::$user->getID(), $contactID);

        self::render('json', [
            'comments' => $comments
        ]);
    }

    public static function save($id, $message, $type, $act_dt) {

        $contactID = \Engine\Session::get('contactID');

        if ($id == '') {

            $createdAt = Carbon::now();

            $id = CommentModel::insertGetId([
                        'user_id' => self::$user->getID(),
                        'contact_id' => $contactID,
                        'message' => $message,
                        'type' => $type,
                        'act_dt' => $act_dt,
                        'created_at' => $createdAt,
                        'state' => 'on'
            ]);

            $res = ['result' => 'ok', 'id' => $id, 'created_at' => $createdAt];
        } else {

            if (self::isMyClientComment($id)) {

                $result = CommentModel::where('id', '=', $id)
                        ->update([
                    'message' => $message,
                    'type' => $type,
                    'act_dt' => $act_dt,
                    'updated_at' => Carbon::now(),
                    'state' => 'on'
                ]);

                $res = ['result' => 'ok', 'data' => $result];
            } else {
                $res = ['result' => 'err'];
            }
        }

        self::render('json', $res);
    }

    public static function delete($id) {
        if (self::isMyClientComment($id)) {
            CommentModel::where('id', '=', $id)
                    ->update(['state' => 'off']);
        }
    }

    public static function important($id) {
        if (self::isMyClientComment($id)) {

            $important = CommentModel::where('id', '=', $id)->value('important');

            CommentModel::where('id', '=', $id)
                    ->update(['important' => ($important == 'y' ? 'n' : 'y')]);
        }
    }

    private static function isMyClientComment($id) {

        try {
            $comment = CommentModel::findOrFail($id);
            return ($comment->user_id == self::$user->getID());
        } catch (ModelNotFoundException $e) {
            return false;
        }
    }

}
