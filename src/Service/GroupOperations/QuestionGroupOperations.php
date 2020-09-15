<?php
declare(strict_types=1);

namespace App\Service\GroupOperations;

use App\Entity\Question;
use App\Repository\QuestionRepository;
use Doctrine\Persistence\ObjectManager;

class QuestionGroupOperations extends GroupOperations
{
    private QuestionRepository $questionRepository;

    public function __construct(QuestionRepository $questionRepository)
    {
        $this->questionRepository=$questionRepository;
        parent::__construct(array('deleteQuestion'));
    }

    protected function doOperation(string $requestKey, string $requestValue, string $selectedItem,
                                   ObjectManager $entityManager):void
    {
        if ($requestKey === 'deleteQuestion') {
            $this->deleteQuestion($selectedItem, $entityManager);
        } else {
            call_user_func(__NAMESPACE__.'\QuestionGroupOperations::'.$requestKey, $selectedItem, $requestValue);
        }
    }

    private function deleteQuestion(string $id, ObjectManager $entityManager): void
    {
        $question=$this->questionRepository->findOneBy(['id'=>$id]);
        $this->deleteAnswer($question, $entityManager);
        $entityManager->remove($question);
    }

    private function deleteAnswer(Question $question, ObjectManager $entityManager): void
    {
        $answers=$question->getAnswers();
        foreach ($answers as $answer) {
            $entityManager->remove($answer);
        }
    }
}