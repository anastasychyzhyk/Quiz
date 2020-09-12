<?php


namespace App\Service;

use Doctrine\Persistence\ObjectManager;

interface GridEditorInterface
{
    public function deleteEntity(string $id, ObjectManager $entityManager);

}