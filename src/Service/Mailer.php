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
    private const EMAIL_SEND_ERROR='An error occurred during sending email. Please contact support.';
    private const CONFIRM_SENDED='Congratulate with successful registration! Please check your email and confirm account.';
    private const EMAIL_SENDED='Message was sent successfully. Please check your email';

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

    public function sendConfirmationMessage(string $subject, User $user): string
    {
        return $this->sendMessage($subject, $user, 'email/confirmation.html.twig', self::CONFIRM_SENDED);
    }

    public function sendRestorePasswordMessage(string $subject, User $user): string
    {
        return $this->sendMessage($subject, $user, 'email/restorePassword.html.twig', self::EMAIL_SENDED);
    }

    private function sendMessage(string $subject, User $user, string $template, string $successMessage): string
    {
        $email = $this->createEmail($user->getEmail(), $subject)
            ->htmlTemplate($template)
            ->context(['user'=>$user,]);
        try {
            $this->mailer->send($email);
            return $successMessage;
        }
        catch (TransportExceptionInterface $e) {
            return self::EMAIL_SEND_ERROR;
        }
    }
}