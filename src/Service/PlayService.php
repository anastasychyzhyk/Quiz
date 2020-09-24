<?php


namespace App\Service;

use App\Entity\Play;
use App\Entity\Question;
use App\Entity\Quiz;
use App\Entity\User;
use App\Repository\AnswerRepository;
use App\Repository\PlayRepository;
use App\Repository\QuestionRepository;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Form\FormInterface;

class PlayService
{
    private PlayRepository $playRepository;
    private ObjectManager $entityManager;
    private ?Play $play;
    private ?Question $currentQuestion;

    public function __construct(PlayRepository $playRepository, ObjectManager $entityManager)
    {
        $this->playRepository = $playRepository;
        $this->entityManager = $entityManager;
        $this->play = null;
        $this->currentQuestion = null;
        return $this;
    }

    public function loadPlay(Quiz $quiz, User $user)
    {
        $this->play = $this->playRepository->findOneBy(['user' => $user, 'quiz' => $quiz]);
        if ($this->play == null) {
            $this->play = new Play($user, $quiz);
            $this->entityManager->persist($this->play);
            $this->entityManager->flush();
        }
    }

    public function getPlay(): ?Play
    {
        return $this->play;
    }

    private function loadQuestion()
    {
        $questions = $this->play->getQuiz()->getQuestion();
        $this->currentQuestion = null;
        if ($this->play->getQuestion() == null) {
            $this->currentQuestion = $questions[0];
        } else {
            for ($i = 0; $i < count($questions) - 1; $i++) {
                if ($questions[$i] == $this->play->getQuestion()) {
                    $this->currentQuestion = $questions[$i + 1];
                    break;
                }
            }
        }
    }

    private function finishPlay()
    {
        $this->play->setIsFinish(true);
        $this->play->setQuestion(null);
        $this->entityManager->flush();
    }

    public function fillParameters(FormInterface $form, AnswerRepository $answerRepository)
    {
        $this->loadQuestion();
        return
            [
                'form' => $form->createView(), 'rightAnswer' => $answerRepository->findRightAnswer($this->currentQuestion)[0],
                'question' => $this->currentQuestion, 'answers' => $this->currentQuestion->getAnswers(),
                'quizName' => $this->play->getQuiz()->getName(), 'play' => $this->play->getId(), 'time' => date("h:i:s")
            ];
    }

    public function saveUserAnswer(QuestionRepository $questionRepository)
    {
        $this->play = $this->playRepository->findOneBy(['id' => $_POST['idPlay']]);
        $currentQuestion = $questionRepository->findOneBy(['id' => $_POST['idQuestion']]);
        $questions = $this->play->getQuiz()->getQuestion();
        for ($i = 0; $i < count($questions); $i++) {
            if ($questions[$i] == $currentQuestion) {
                if ($questions[$i + 1] == null) {
                    $this->finishPlay();
                } else {
                    $this->play->setQuestion($currentQuestion);
                }
                break;
            }
        }
        if ($_POST['isRightAnswer'] == 'true') {
            $this->play->setRightAnswersCount($this->play->getRightAnswersCount() + 1);
        }
        $this->entityManager->flush();
    }
}