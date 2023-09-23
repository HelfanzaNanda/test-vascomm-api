<?php

namespace App\Helpers;

use App\Mail\DynamicMail;
use Illuminate\Support\Facades\{Mail, Log};


class MailHelper
{
    public static function send($email, $title, $content)
    {
        try {
			Mail::to($email)->send(new DynamicMail( $title, $content));
            Log::channel('mail')->info($email, [$title, 'send email successfully']);
		} catch (\Throwable $th) {
            Log::channel('mail')->error($email, [
                [
                    'title' => $title, 
                    'message' => $th->getMessage(), 
                    'file' => $th->getFile() .'#'. $th->getLine() , 
                ]
            ]);
		}
    }
}
