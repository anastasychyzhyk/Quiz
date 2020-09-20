<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Quiz;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr;

/**
 * @method Quiz|null find($id, $lockMode = null, $lockVersion = null)
 * @method Quiz|null findOneBy(array $criteria, array $orderBy = null)
 * @method Quiz[]    findAll()
 * @method Quiz[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuizRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Quiz::class);
    }

    public function findMaxAnswerQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('qMaxAnswer')
            ->select('max(pMaxAnswer.rightAnswersCount)')
            ->innerJoin('qMaxAnswer.plays', 'pMaxAnswer')
            ->where('pMaxAnswer.isFinish=true and qMaxAnswer=q');
    }

    public function findMinTimeQuery(): QueryBuilder
    {
        $minTimeQuery = $this->createQueryBuilder('qMinTime');
        $minTimeQuery->select('min(pMinTime.time)')
            ->innerJoin('qMinTime.plays', 'pMinTime')
            ->where($minTimeQuery->expr()->andX(
                $minTimeQuery->expr()->eq('pMinTime.isFinish','true'),
                $minTimeQuery->expr()->eq ('qMinTime','q'),
                $minTimeQuery->expr()->in('pMinTime.rightAnswersCount', $this->findMaxAnswerQuery()->getDql()))
            );
        return $minTimeQuery;
    }

    public function findWinnerIdQuery(): QueryBuilder
    {
        $winnerIdQuery = $this->createQueryBuilder('qWinnerId');
        $winnerIdQuery ->select('pWinnerId.id')
            ->innerJoin('qWinnerId.plays', 'pWinnerId')
            ->where($winnerIdQuery->expr()->andX(
                $winnerIdQuery->expr()->eq('pWinnerId.isFinish','true'),
                $winnerIdQuery->expr()->eq ('qWinnerId','q'),
                $winnerIdQuery->expr()->in('pWinnerId.time', $this->findMinTimeQuery()->getDql()))
            );
        return $winnerIdQuery;
    }

    public function findByTextQuery(string $searchedText, array $filters=null)
    {
        $qb = $this->createQueryBuilder('q');
        $qb ->select('q.id', 'q.name', 'q.isActive', "count(distinct p.user) as count",
                "CONCAT(u.name, ' ', u.surname, ' ', u.patronymic) as userName")
            ->leftJoin('q.plays', 'p')
            ->leftJoin('q.plays', 'pWinner', Expr\Join::WITH,
                $qb->expr()->andX(
                    $qb->expr()->eq('pWinner.isFinish', 'true'),
                    $qb->expr()->in('pWinner.id', $this->findWinnerIdQuery()->getDql())
                )
            )
            ->leftJoin('pWinner.user', 'u')
            ->where('q.name LIKE :searchedText')
            ->setParameter('searchedText', '%' . $searchedText . '%')
            ->groupBy('q.id');
        if($filters!=null) {
            $qb=$this->setParametersFromArray($qb, $filters);
        }
        return $qb->getQuery();
    }

    private function setParametersFromArray(QueryBuilder $qb, array $filters)
    {
        if ((array_key_exists('isActive', $filters)) && ($filters['isActive'] != null)) {
            $qb->andWhere('q.isActive = :isActive')
                ->setParameter('isActive', $filters['isActive']);
        }
        return $qb;
    }
}
