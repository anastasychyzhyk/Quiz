<?php

namespace App\Controller;

use App\Entity\Quiz;
use App\Form\EditQuizType;
use App\Repository\AnswerRepository;
use App\Repository\QuizRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EditQuizController extends AbstractController
{
    /**
     * @Route("/admin/edit/quiz")
     */
    public function index(): Response
    {
        return $this->redirectToRoute('edit_quiz');
    }

    /**
     * @Route("/admin/{_locale<%app.supported_locales%>}/edit/quiz/{id}", name="edit_quiz")
     * @param Request $request
     * @param QuizRepository $quizRepository
     * @param string $id
     * @return Response
     */
    public function edit(Request $request,QuizRepository $quizRepository, string $id): Response
    {
        $quiz = $quizRepository->findOneBy(['id' => $id]);
        if (!$quiz) {
            throw $this->createNotFoundException();
        }
        return $this->processRequest($request, $quiz);
    }

    /**
     * @Route("/admin/new/question")
     */
    public function noLocaleNewQuestion(): Response
    {
        return $this->redirectToRoute('new_quiz');
    }

    /**
     * @Route("/admin/{_locale<%app.supported_locales%>}/new/quiz", name="new_quiz")
     * @param Request $request
     * @return Response
     */
    public function newQuiz(Request $request): Response
    {
        $quiz=new Quiz();
        return $this->processRequest($request, $quiz);
    }

    private function processRequest(Request $request, Quiz $quiz): Response
    {
        $form = $this->createForm(EditQuizType::class, $quiz);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            return $this->redirectToRoute('question_editor');
        }
        return $this->render('edit_quiz/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
