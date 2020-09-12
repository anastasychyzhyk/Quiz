<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Question;
use App\Repository\QuestionRepository;
use Doctrine\Persistence\ObjectManager;

class QuestionEditor implements GridEditorInterface
{
    private QuestionRepository $questionRepository;

    public function __construct(QuestionRepository $questionRepository)
    {
        $this->questionRepository=$questionRepository;
    }

    public function deleteEntity(string $id, ObjectManager $entityManager): void
    {
            $question=$this->questionRepository->findOneBy(['id'=>$id]);
            $this->deleteAnswer($question, $entityManager);
            $entityManager->remove($question);
    }

    public function deleteAnswer(Question $question, ObjectManager $entityManager): void
    {
        $answers=$question->getAnswers();
        foreach ($answers as $answer) {
            $entityManager->remove($answer);
        }
        $entityManager->flush();
    }

    private function changeRightAnswer(Question $question, int $rightAnswerPosition): Question
    {
        foreach ($question->getAnswers() as $answer) {
            $answer->setIsTrue(false);
        }
        $question->getAnswers()[$rightAnswerPosition]->setIsTrue(true);
        return $question;
    }

    public function changeQuestion(Question $question, int $rightAnswerPosition, ObjectManager $entityManager): void
    {
        $question=$this->changeRightAnswer($question, $rightAnswerPosition);
        $entityManager->persist($question);
        $entityManager->flush();
    }
}