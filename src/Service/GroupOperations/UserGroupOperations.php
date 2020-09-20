<?php
declare(strict_types=1);

namespace App\Service\GroupOperations;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Persistence\ObjectManager;

class UserGroupOperations extends GroupOperations
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
        parent::__construct(array('setRole', 'setStatus', 'deleteUser'));
    }

    protected function doOperation(string $requestKey, string $requestValue, string $selectedItem,
                                   ObjectManager $entityManager): void
    {
        if ($requestKey === 'deleteUser') {
            $this->deleteUser($selectedItem, $entityManager);
        } else {
            call_user_func(__NAMESPACE__ . '\UserGroupOperations::' . $requestKey, $selectedItem, $requestValue);
        }
    }

    private function deleteUser(string $id, ObjectManager $entityManager): void
    {
        $user = $this->userRepository->findOneBy(['id' => $id]);
        $this->deletePlays($user, $entityManager);
        $entityManager->remove($user);
    }

    private function setStatus(string $id, string $status): void
    {
        $this->userRepository->findOneBy(['id' => $id])->setStatus($status);
    }

    private function setRole(string $id, string $role): void
    {
        $this->userRepository->findOneBy(['id' => $id])->setRole($role);
    }

    private function deletePlays(User $user, ObjectManager $entityManager): void
    {
        $plays = $user->getPlays();
        foreach ($plays as $play) {
            $entityManager->remove($play);
        }
    }
}