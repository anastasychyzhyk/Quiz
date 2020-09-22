<?php
declare(strict_types=1);

namespace App\Service\GroupOperations;

use App\Entity\Quiz;
use App\Repository\QuizRepository;
use Doctrine\Persistence\ObjectManager;

class QuizGroupOperations extends GroupOperations
{
    private QuizRepository $quizRepository;

    public function __construct(QuizRepository $questionRepository)
    {
        $this->quizRepository = $questionRepository;
        parent::__construct(array('deleteQuiz'));
    }

    protected function doOperation(
        string $requestKey,
        string $requestValue,
        string $selectedItem,
        ObjectManager $entityManager
    ): void
    {
        if ($requestKey === 'deleteQuiz') {
            $this->deleteQuiz($selectedItem, $entityManager);
        } else {
            call_user_func(__NAMESPACE__ . '\QuestionGroupOperations::' . $requestKey, $selectedItem, $requestValue);
        }
    }

    private function deleteQuiz(string $id, ObjectManager $entityManager): void
    {
        $quiz = $this->quizRepository->findOneBy(['id' => $id]);
        $this->deletePlay($quiz, $entityManager);
        $entityManager->remove($quiz);
    }

    private function deletePlay(Quiz $quiz, ObjectManager $entityManager): void
    {
        $plays = $quiz->getPlays();
        foreach ($plays as $play) {
            $entityManager->remove($play);
        }
    }
}
