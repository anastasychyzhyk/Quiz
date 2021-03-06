<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Question;
use App\Entity\Quiz;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Question|null find($id, $lockMode = null, $lockVersion = null)
 * @method Question|null findOneBy(array $criteria, array $orderBy = null)
 * @method Question[]    findAll()
 * @method Question[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Question::class);
    }

    public function findByTextQuery(string $searchedText, int $limit=0)
    {
        $qb = $this->createQueryBuilder('q');
        $qb->where('q.text LIKE :searchedText')
            ->setParameter('searchedText', '%' . $searchedText . '%');
        if ($limit > 0) {
            $qb->setMaxResults($limit);
        }
        return $qb
            ->orderBy('q.text', 'ASC')
            ->getQuery()
            ;
    }

    public function findByQuizQuery(Quiz $quiz)
    {
        $qb = $this->createQueryBuilder('q');
        $qb->innerJoin('q.quizzes', 'quizzes')
        ->where('quizzes.id = :quizzesId')
            ->setParameter('quizzesId', $quiz->getId())
        ;
        return $qb
            ->getQuery()
            ;
    }
}
