<?php

namespace App\service;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;


class NewMessage
{
    private $mail;

    /**
     * NewMessage constructor.
     * @param $mail
     */
    public function __construct(MailerInterface $mail)
    {
        $this->mail = $mail;
    }

    public function getHappyMessage(): string
    {
        $messages = [
            'You did it! You updated the system! Amazing!',
            'That was one of the coolest updates I\'ve seen all day!',
            'Great work! Keep going!',
        ];

        $index = array_rand($messages);

        return $messages[$index];
    }
    public function sendMail(){
        $email = (new Email())
            ->from('insorti@gmail.com')
            ->to('jo@ascetic.io')
            ->subject('Site update just happened!')
            ->text('Someone just updated the site. We told them: '.$this->getHappyMessage());

        $this->mail->send($email);
        return "Mensaje enviado";
    }
}