<?php
declare(strict_types=1);

namespace App\Service;
use App\Entity\User;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;

class Mailer
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer=$mailer;
    }

    private function createEmail(string $to, string $subject):TemplatedEmail
    {
        return (new TemplatedEmail())
            ->from(new Address('quiz-mailer@mail.ru', 'Quiz'))
            ->to($to)
            ->subject($subject);
    }

    public function sendConfirmationMessage(string $subject, User $user): bool
    {
        $email = $this->createEmail($user->getEmail(), $subject)
			->htmlTemplate('email/confirmation.html.twig')
			->context(['user'=>$user,]);
        try {
            $this->mailer->send($email);
            return true;
        }
        catch (TransportExceptionInterface $e) {
            return false;
        }
    }
}