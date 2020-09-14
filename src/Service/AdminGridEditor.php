<?php
declare(strict_types=1);

namespace App\Service;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\HttpFoundation\Request;

class AdminGridEditor
{
    private Request $request;
    private GridEditorInterface $editor;
    private ServiceEntityRepository $repository;
    private array $processedOperations;
    private ObjectManager $entityManager;

    public function __construct(Request $request, GridEditorInterface $editor, ServiceEntityRepository $repository,
                                array $processedOperations, ObjectManager $entityManager)
    {
        $this->request = $request;
        $this->editor = $editor;
        $this->repository = $repository;
        $this->processedOperations = $processedOperations;
        $this->entityManager = $entityManager;
    }

    public function processRequest()
    {
        if (($this->request->request->get('find') === null) && ($this->request->request->get('filter') === null)) {
            $selectedItems = $this->request->request->get('checkbox');
            if ($selectedItems !== null) {
                $this->processRequestKeys($selectedItems);
            }
        }
    }

    private function getFilters()
    {
        $filters = null;
        if ($this->request->request->get('filter') !== null) {
            $role = $this->request->request->get('role') ?? '';
            $status = $this->request->request->get('status') ?? '';
            $filters = ['role' => $role, 'status' => $status];
        }
        return $filters;
    }

    public function getPagination(PaginatorInterface $paginator): PaginationInterface
    {
        $users = $this->repository->findByTextQuery($this->request->request->get('searchedText') ?? '', $this->getFilters());
        return $paginator->paginate($users, $this->request->query->getInt('page', 1), 20);
    }

    private function processRequestKeys (array $selectedItems)
    {
        foreach ($this->request->request->keys() as $requestData) {
            if (array_search($requestData, $this->processedOperations) === false) {
                continue;
            }
            $this->processGroupOperation($requestData, $selectedItems);
        }
        $this->entityManager->flush();
    }

    private function processGroupOperation(string $requestData, array $selectedItems)
    {
        foreach ($selectedItems as $selectedItem) {
            if ($requestData === 'deleteEntity') {
                $this->editor->deleteEntity($selectedItem, $this->entityManager);
            } else {
                call_user_func(__NAMESPACE__ . '\UserEditor::' . $requestData, $selectedItem, $this->repository);
            }
        }
    }
}