<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\Persistence\ObjectManager;

class UserEditor implements GridEditorInterface
{
    private UserPasswordEncoderInterface $passwordEncoder;
    private UserRepository $userRepository;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, UserRepository $userRepository)
    {
        $this->passwordEncoder=$passwordEncoder;
        $this->userRepository=$userRepository;
    }

    private function encodeAndSetPassword(User $user): User
    {
        $password = $this->passwordEncoder->encodePassword($user, $user->getPassword());
        $user->setPassword($password);
        return $user;
    }

    public function createUser(User $user, ObjectManager $em): ?User
    {
        $user=$this->encodeAndSetPassword($user);
        $user->setConfirmationCode((new CodeGenerator())->getConfirmationCode());
        $em->persist($user);
        $em->flush();
        return $user;
    }

    public function deleteEntity(array $ids, ObjectManager $em): void
    {
        foreach ($ids as $id)
        {
            $user=$this->userRepository->findOneBy(['id'=>$id]);
            $this->deletePlays($user, $em);
            $em->remove($user);
        }
        $em->flush();
    }

    public function deletePlays(User $user, ObjectManager $em): void
    {
        $plays=$user->getPlays();
        foreach ($plays as $play) {
            $em->remove($play);
        }
        $em->flush();
    }
}