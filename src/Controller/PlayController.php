<?php

namespace App\Controller;

use App\Entity\Play;
use App\Form\QuizType;
use App\Repository\AnswerRepository;
use App\Repository\PlayRepository;
use App\Repository\QuestionRepository;
use App\Repository\QuizRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlayController extends AbstractController
{

    /**
     * @Route("/{_locale<%app.supported_locales%>}/play/{id}", name="play")
     * @param Request $request
     * @param PlayRepository $playRepository
     * @param QuizRepository $quizRepository
     * @param AnswerRepository $answerRepository
     * @param string $id
     * @return Response
     */
    public function index(Request $request, PlayRepository $playRepository, QuizRepository $quizRepository, AnswerRepository $answerRepository, string $id): Response
    {
        $quiz = $quizRepository->findOneBy(['id' => $id]);
        if ($quiz == null) {
            throw $this->createNotFoundException();
        }
        $play = $playRepository->findOneBy(['user' => $this->getUser(), 'quiz' => $quiz]);
        if ($play == null) {
            $play = new Play($this->getUser(), $quiz);
            $this->getDoctrine()->getManager()->persist($play);
            $this->getDoctrine()->getManager()->flush();
        }
        $questions = $quiz->getQuestion();
        $currentQuestion = null;
        if ($play->getQuestion() == null) {
            $currentQuestion = $questions[0];
        } else {
            for ($i = 0; $i < count($questions); $i++) {
                if ($i == (count($questions) - 1)) {
                    $this->addFlash('notice', 'Congratulations! You are finish quiz!');
                    return $this->redirectToRoute('quiz_finish', ['id' => $id]);
                }
                if ($questions[$i] == $play->getQuestion()) {
                    $currentQuestion = $questions[$i + 1];
                    break;
                }
            }
        }
        $rightAnswer = $answerRepository->findRightAnswer($currentQuestion);
        $form = $this->createForm(QuizType::class, $quiz);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
        }
        return $this->render('play/index.html.twig', [
            'form' => $form->createView(), 'rightAnswer' => $rightAnswer[0],
            'question' => $currentQuestion, 'answers' => $currentQuestion->getAnswers(),
            'quizName' => $quiz->getName(), 'play' => $play->getId(), 'time'=>date("h:i:s")
        ]);
    }

    /**
     * @Route("/save/select", name="save_select")
     * @param PlayRepository $playRepository
     * @param QuestionRepository $questionRepository
     * @param AnswerRepository $answerRepository
     * @return JsonResponse
     */
    public function saveSelect(PlayRepository $playRepository, QuestionRepository $questionRepository, AnswerRepository $answerRepository): JsonResponse
    {
        $play = $playRepository->findOneBy(['id' => $_POST['idPlay']]);
        $currentQuestion = $questionRepository->findOneBy(['id' => $_POST['idQuestion']]);
        $play->setQuestion($currentQuestion);
        if ($_POST['isRightAnswer']=='true') {
            $play->setRightAnswersCount($play->getRightAnswersCount()+1);
        }
        $this->getDoctrine()->getManager()->flush();
        return new JsonResponse(array());
    }
}
