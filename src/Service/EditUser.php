<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class EditUser
{
    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function encodeAndSetPassword(User $user): User
    {
        $password = $this->passwordEncoder->encodePassword($user, $user->getPassword());
        return $user->setPassword($password);
    }

    public function registerUser(User $user, ObjectManager $entityManager): ?User
    {
        $entityManager->persist($user);
        $user = $this->encodeAndSetPassword($user);
        $user = $this->setConfirmationCode($user);
        $entityManager->flush();
        return $user;
    }

    public function setConfirmationCode(User $user, ObjectManager $entityManager = null): ?User
    {
        $user->setConfirmationCode((new CodeGenerator())->getConfirmationCode());
        if ($entityManager != null) {
            $entityManager->flush();
        }
        return $user;
    }
}