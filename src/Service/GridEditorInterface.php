<?php


namespace App\Service;


use Doctrine\Persistence\ObjectManager;

interface GridEditorInterface
{
    public function deleteEntity(array $ids, ObjectManager $em);

}