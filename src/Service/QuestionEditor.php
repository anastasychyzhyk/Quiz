<?php


namespace App\Service;

use App\Entity\Question;
use App\Repository\QuestionRepository;
use Doctrine\Persistence\ObjectManager;
use http\Env\Request;

class QuestionEditor
{
    public function deleteQuestion(array $ids, QuestionRepository $questionRepository, ObjectManager $em)
    {
        foreach ($ids as $id)
        {
            $question=$questionRepository->findOneBy(['id'=>$id]);
            $this->deleteAnswer($question, $em);
            $em->remove($question);
        }
        $em->flush();
    }
    public function deleteAnswer(Question $question, ObjectManager $em)
    {
        $answers=$question->getAnswers();
        foreach ($answers as $answer) {
            $em->remove($answer);
        }
        $em->flush();
    }
}