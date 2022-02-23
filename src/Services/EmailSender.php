<?php
namespace App\Services;

use App\Entity\EmailModel;
use App\Entity\User;
use Mailjet\Client;
use Mailjet\Resources;


class EmailSender{

// vendor/guzzlehttp/guzzle/src/Handler/Client.php et à la ligne 233 changer le 'verify' = true, en 'verify' = false

    public function sendEmailNotificationByMailJet(User $user, EmailModel $email){
     
        $mj = new Client(getenv('MJ_APIKEY_PUBLIC'), getenv('MJ_APIKEY_PRIVATE'),true,['version' => 'v3.1']);
        $body = [
        'Messages' => [
          [
            'From' => [
              'Email' => "faridtah97@gmail.com",
              'Name' => "BoutiqueEnLigne Contact"
            ],
            'To' => [
              [
                'Email' => $user->getEmail(),
                'Name' => $user->getFullName()
              ]
            ],
            'TemplateID' => 3662270,
            'TemplateLanguage' => true,
            'Subject' => $email->getSubject(),
            'Variables' => [
                'title' => $email->getTitle(),
                'content' => $email->getContent()
            ]
                ]
                ]
            ];
            $response = $mj->post(Resources::$Email, ['body' => $body]);
            // $response->success() && dd($response->getData());

        }
}