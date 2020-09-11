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

    public function deleteEntity(array $ids, ObjectManager $em): void
    {
        foreach ($ids as $id)
        {
            $question=$this->questionRepository->findOneBy(['id'=>$id]);
            $this->deleteAnswer($question, $em);
            $em->remove($question);
        }
        $em->flush();
    }

    public function deleteAnswer(Question $question, ObjectManager $em): void
    {
        $answers=$question->getAnswers();
        foreach ($answers as $answer) {
            $em->remove($answer);
        }
        $em->flush();
    }

    private function changeRightAnswer(Question $question, int $rightAnswerPosition): Question
    {
        foreach ($question->getAnswers() as $answer) {
            $answer->setIsTrue(false);
        }
        $question->getAnswers()[$rightAnswerPosition]->setIsTrue(true);
        return $question;
    }

    public function changeQuestion(Question $question, int $rightAnswerPosition, ObjectManager $em): void
    {
        $question=$this->changeRightAnswer($question, $rightAnswerPosition);
        $em->persist($question);
        $em->flush();
    }
}