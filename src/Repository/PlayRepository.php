<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Play;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Play|null find($id, $lockMode = null, $lockVersion = null)
 * @method Play|null findOneBy(array $criteria, array $orderBy = null)
 * @method Play[]    findAll()
 * @method Play[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlayRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Play::class);
    }

    public function findResults(string $searchedId)
    {
        $qb = $this->createQueryBuilder('p');
        $qb->select("u.id", "CONCAT(u.name, ' ', u.surname, ' ', u.patronymic) as userName", "p.rightAnswersCount as result")
            ->innerJoin('p.user', 'u')
            ->innerJoin('p.quiz', 'q', 'with', 'q.id=:searchedId')
            ->setParameter('searchedId', $searchedId)
            ->where('p.isFinish=true')
            ->orderBy('p.rightAnswersCount', 'DESC')
            ->addOrderBy('p.time');
        return $qb->getQuery()->getResult();
    }
}
