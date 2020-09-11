<?php


namespace App\Service;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\HttpFoundation\Request;

class AdminGridEditor
{
    public function getSearchedText(Request $request): string
    {
        return $request->request->get('find') !== null? $request->request->get('searchedText'):'';
    }

    public function processDelete(Request $request, GridEditorInterface $editor, ObjectManager $em): void
    {
        if (($request->request->get('delete') !== null) && ($request->request->get('checkbox') !== null)) {
            $editor->deleteEntity($request->request->get('checkbox'), $em);
        }
    }

    public function getPagination(ServiceEntityRepository $repository, string $condition, Request $request,
                                  PaginatorInterface $paginator): PaginationInterface
    {
        $users = $repository->findByTextQuery($condition);
        return $paginator->paginate($users, $request->query->getInt('page', 1), 20);
    }
}