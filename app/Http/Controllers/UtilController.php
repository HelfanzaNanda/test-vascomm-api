<?php

namespace App\Http\Controllers;

use App\Helpers\MailHelper;
use App\Helpers\ResponseHelper;
use App\Helpers\StringHelper;
use Illuminate\Http\Request;

class UtilController extends Controller
{
    public function sendEmail(Request $request)
    {
        $email = $request->email;
        $password = StringHelper::getRandomString(8);

        $title = "welcome to join " . env('APP_NAME');
        $content = "here is your login account, username = $email, password = $password";
        MailHelper::send($email, $title, $content);

        return response()->json(ResponseHelper::success(), 200);
    }
}
