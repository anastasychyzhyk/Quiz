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

    public function findByTextQuery(string $searchedText, array $filters=null)
    {
        $qb = $this->createQueryBuilder('u');
        $qb->where('u.surname LIKE :searchedText')
            ->setParameter('searchedText', '%' . $searchedText . '%');
        if ($filters!=null) {
            $qb=$this->setParametersFromArray($qb, $filters);
        }
   
        return $qb
            ->orderBy('u.name', 'ASC')
            ->getQuery()
            ;
    }

    private function setParametersFromArray(QueryBuilder $qb, array $filters)
    {
        while ($option = current($filters)) {
            $qb->andWhere('u.' . key($filters) . ' = :'.key($filters))
                ->setParameter(key($filters), $option);
            next($filters);
        }
        return $qb;
    }
}
