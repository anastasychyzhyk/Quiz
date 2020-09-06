<?php


namespace App\Service;

use App\Entity\Answer;
use App\Entity\Question;
use App\Repository\QuestionRepository;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;

class QuestionEditor
{
    public function deleteQuestion(array $ids, QuestionRepository $questionRepository, ObjectManager $em): void
    {
        foreach ($ids as $id)
        {
            $question=$questionRepository->findOneBy(['id'=>$id]);
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

    public function saveQuestion(Question $question,ObjectManager $em): void
    {
        $em->persist($question);
        $em->flush();
    }
}