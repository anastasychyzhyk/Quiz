<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findByTextQuery(string $searchedText, int $limit = 0, array $filters = null)
    {
        $qb = $this->createQueryBuilder('u');
        $qb->where('u.surname LIKE :searchedText')
            ->setParameter('searchedText', '%' . $searchedText . '%');
        if ($filters != null) {
            $qb = $this->setParametersFromArray($qb, $filters);
        }
        if ($limit > 0) {
            $qb->setMaxResults($limit);
        }
        return $qb
            ->orderBy('u.name', 'ASC')
            ->getQuery();
    }

    private function setParametersFromArray(QueryBuilder $qb, array $filters)
    {
        if (array_key_exists('role', $filters) && $filters['role']) {
            $qb->andWhere('u.role = :roleFilter')
                ->setParameter('roleFilter', $filters['role']);
        }
        if (array_key_exists('status', $filters) && $filters['status']) {
            $qb->andWhere('u.status = :statusFilter')
                ->setParameter('statusFilter', $filters['status']);
        }
        return $qb;
    }
}