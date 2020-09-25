<?php
declare(strict_types=1);

namespace App\Service;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use App\Service\GroupOperations\GroupOperations;
use Doctrine\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\Form\FormInterface;
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

    private function getFilters(FormInterface $form)
    {
        $filters = array();
        if ($form->getData()) {
            if (array_key_exists('role', $form->getData())) {
                $filters['role'] = $form->get('role')->getData() ?? '';
            }
            if (array_key_exists('status', $form->getData())) {
                $filters['status'] = $form->get('status')->getData() ?? '';
            }
            if (array_key_exists('isActive', $form->getData())) {
                $filters['isActive'] = $form->get('isActive')->getData() ?? '';
            }
        }
        return $filters;
    }

    public function getPagination(PaginatorInterface $paginator, FormInterface $form): PaginationInterface
    {
        $searchedText = $form->getData() ? $form->get('searchedText')->getData() ?? '' : '';
        $query = $this->repository->findByTextQuery($searchedText, 0, $this->getFilters($form));
        return $paginator->paginate($query, $this->request->query->getInt('page', 1), 5);
    }
}
