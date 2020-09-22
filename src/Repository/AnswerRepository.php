<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Answer;
use App\Entity\Question;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Answer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Answer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Answer[]    findAll()
 * @method Answer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnswerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Answer::class);
    }
    public function findRightAnswer(Question $question): array
    {
        $qb = $this->createQueryBuilder('a');
        $qb
            ->where('a.question=:question')
            ->setParameter('question', $question)
            ->andWhere('a.isTrue =true');
        return $qb->getQuery()->getResult();
    }
}
