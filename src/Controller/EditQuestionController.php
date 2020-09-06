<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Question;
use App\Form\EditQuestionType;
use App\Service\QuestionEditor;
use App\Repository\QuestionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class EditQuestionController extends AbstractController
{
    private QuestionEditor $questionEditor;
    public function __construct(QuestionEditor $questionEditor)
    {
        $this->questionEditor=$questionEditor;
    }

    /**
     * @Route("/{_locale<%app.supported_locales%>}/edit/question/{id}", name="edit_question")
     * @param Request $request
     * @param QuestionRepository $questionRepository
     * @param int $id
     * @return Response
     */
    public function edit(Request $request,QuestionRepository $questionRepository, int $id): Response
    {
        $question = $questionRepository->findOneBy(['id'=>$id]);
        return $this->processRequest($request, $question);
    }

    /**
     * @Route("/{_locale<%app.supported_locales%>}/new/question", name="new_question")
     * @param Request $request
     * @param QuestionRepository $questionRepository
     * @return Response
     */
    public function newQuestion(Request $request, QuestionRepository $questionRepository): Response
    {
        $question=new Question();
        return $this->processRequest($request, $question);

    }

    private function processRequest(Request $request, Question $question)
    {
        $form = $this->createForm(EditQuestionType::class, $question);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $question=$form->getData();
            $this->questionEditor->saveQuestion($question, $this->getDoctrine()->getManager());
            return $this->redirectToRoute('question_editor');
        }
        return $this->render('edit_question/index.html.twig', [
            'controller_name' => 'EditQuestionController','form' => $form->createView(),
        ]);
    }
}
