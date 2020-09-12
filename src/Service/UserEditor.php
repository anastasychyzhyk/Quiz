<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
        return $user->setPassword($password);
    }

    public function createUser(User $user, ObjectManager $entityManager): ?User
    {
        $user=$this->encodeAndSetPassword($user);
        $user->setConfirmationCode((new CodeGenerator())->getConfirmationCode());
        $entityManager->persist($user);
        $entityManager->flush();
        return $user;
    }

    public function deleteEntity(string $id, ObjectManager $entityManager): void
    {
        $user = $this->userRepository->findOneBy(['id' => $id]);
        $this->deletePlays($user, $entityManager);
        $entityManager->remove($user);
    }

    public function activateUsers(string $id, UserRepository $userRepository): void
    {
        $userRepository->findOneBy(['id'=>$id])->activate();
    }

    public function blockUsers(string $id, UserRepository $userRepository): void
    {
        $userRepository->findOneBy(['id'=>$id])->block();
    }

    public function setUser(string $id, UserRepository $userRepository): void
    {
        $userRepository->findOneBy(['id'=>$id])->setUser();
    }

    public function setAdmin(string $id, UserRepository $userRepository): void
    {
        $userRepository->findOneBy(['id' => $id])->setAdmin();
    }

    public function deletePlays(User $user, ObjectManager $entityManager): void
    {
        $plays=$user->getPlays();
        foreach ($plays as $play) {
            $entityManager->remove($play);
        }
        $entityManager->flush();
    }
}