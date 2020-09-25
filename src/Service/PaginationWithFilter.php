<?php
declare(strict_types=1);

namespace App\Service;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class PaginationWithFilter
{
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

    public function getPagination(
        PaginatorInterface $paginator,
        FormInterface $form,
        Request $request,
        ServiceEntityRepository $repository): PaginationInterface
    {
        $searchedText = $form->getData() ? $form->get('searchedText')->getData() ?? '' : '';
        $query = $repository->findByTextQuery($searchedText, 0, $this->getFilters($form));
        return $paginator->paginate($query, $request->query->getInt('page', 1), 5);
    }
}
