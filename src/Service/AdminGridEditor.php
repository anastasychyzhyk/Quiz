<?php
declare(strict_types=1);

namespace App\Service;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use App\Service\GroupOperations\GroupOperations;
use Doctrine\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\HttpFoundation\Request;

class AdminGridEditor
{
    private Request $request;
    private GroupOperations $editor;
    private ServiceEntityRepository $repository;
    private ObjectManager $entityManager;

    public function __construct(
        Request $request,
        GroupOperations $editor,
        ServiceEntityRepository $repository,
        ObjectManager $entityManager
    )
    {
        $this->request = $request;
        $this->editor = $editor;
        $this->repository = $repository;
        $this->entityManager = $entityManager;
    }

    public function processRequest()
    {
        if (($this->request->request->get('find') === null) && ($this->request->request->get('filter') === null)) {
            $selectedItems = $this->request->request->get('checkbox');
            if ($selectedItems !== null) {
                $this->editor->processGroupOperation($this->request, $selectedItems, $this->entityManager);
            }
        }
    }

    private function getFilters()
    {
        $filters = array();
        if ($this->request->request->get('filter') !== null) {
            if ($this->request->request->get('role') != '') {
                $filters['role'] = $this->request->request->get('role');
            }
            if ($this->request->request->get('status') != '') {
                $filters['status'] = $this->request->request->get('status');
            }
            if ($this->request->request->get('isActive') != '') {
                $filters['isActive'] = $this->request->request->get('isActive');
            }
        }
        return $filters;
    }

    public function getPagination(PaginatorInterface $paginator): PaginationInterface
    {
        $query = $this->repository->findByTextQuery($this->request->request->get('searchedText') ?? '', 0, $this->getFilters());
        if($this->request->request->get('find')==null)
        return $paginator->paginate($query, $this->request->query->getInt('page', 1), 20);
        else return $paginator->paginate($query, 1, 20);
    }
}
