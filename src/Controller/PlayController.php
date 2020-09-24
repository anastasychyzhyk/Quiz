<?php

namespace App\Controller;

use App\Form\QuizType;
use App\Repository\AnswerRepository;
use App\Repository\PlayRepository;
use App\Repository\QuestionRepository;
use App\Repository\QuizRepository;
use App\Service\PlayService;
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
     * @param QuizRepository $quizRepository
     * @param AnswerRepository $answerRepository
     * @param PlayRepository $playRepository
     * @param string $id
     * @return Response
     */
    public function index(
        Request $request,
        QuizRepository $quizRepository,
        AnswerRepository $answerRepository,
        PlayRepository $playRepository,
        string $id): Response
    {
        $quiz = $quizRepository->findOneBy(['id' => $id]);
        if ($quiz == null) throw $this->createNotFoundException();
        $playService = new PlayService($playRepository, $this->getDoctrine()->getManager());
        $playService->loadPlay($quiz, $this->getUser());
        if ($playService->getPlay()->getIsFinish()) return $this->redirectToRoute('quiz_finish', ['id' => $id]);
        $form = $this->createForm(QuizType::class, $quiz)->handleRequest($request);
        return $this->render('play/index.html.twig', $playService->fillParameters($form, $answerRepository));
    }

    /**
     * @Route("/save/select", name="save_select")
     * @param PlayRepository $playRepository
     * @param QuestionRepository $questionRepository
     * @return JsonResponse
     */
    public function saveSelect(PlayRepository $playRepository, QuestionRepository $questionRepository): JsonResponse
    {
        (new PlayService($playRepository, $this->getDoctrine()->getManager()))->saveUserAnswer($questionRepository);
        return new JsonResponse(array());
    }
}
