<?php
namespace App\Solid\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Mail;
/**
 * Import Interfaces
 */
use App\Solid\Interfaces\EmailRepositoryInterface;


class EmailRepository implements EmailRepositoryInterface
{
    public function sendEmail(array $emailArray){

        // $data['data'] = json_decode($emailArray['data'], true);
        // $data['data'] = $emailArray['data'];
        // $data['body'] = $emailArray['body'];
        $data = array(
            'data' => $emailArray['data'],
            'body' => $emailArray['body']
        );

        if(isset($emailArray['quote_data'])){
            $data['quote_data'] = $emailArray['quote_data'];
        }

        Mail::send("mail.{$emailArray['emailFilePath']}", $data , function($message) use ($emailArray) {
            $message->bcc('cpagtalunan@pricon.ph');
            $message->to($emailArray['to']);
            $message->cc($emailArray['cc']);
            if($emailArray['bcc']){
                $message->bcc($emailArray['bcc']);
            }
            $message->subject($emailArray['subject']);
        });
    }
}