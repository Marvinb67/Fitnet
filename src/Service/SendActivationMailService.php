<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class SendActivationMailService 
{
    public function __construct( 
        private readonly MailerInterface $mailer, 
        private VerifyEmailHelperInterface $helper
        ){}

    public function send( 
    string $from,
    string $to,
    string $subject,
    string $token,
    string $signature
    )
    {
        $email = (new TemplatedEmail())
            // On attribue l'expéditeur
            ->from($from)
            // On attribue le destinataire
            ->to($to)
            // On crée le texte avec la vue
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject($subject)
            ->htmlTemplate("registration/confirmation_email.html.twig")
            ->context(['signedUrl' => $signature]);
        $this->mailer->send($email);
    }
}