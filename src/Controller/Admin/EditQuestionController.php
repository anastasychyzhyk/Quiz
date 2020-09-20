<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Repository\AnswerRepository;
use App\Entity\Question;
use App\Form\EditQuestionType;
use App\Service\EditQuestion;
use App\Repository\QuestionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class EditQuestionController extends AbstractController
{
    private EditQuestion $questionEditor;
    private const RIGHT_ANSWERS_COUNT_ERROR="Error occurred! More than 1 right answer";

    public function __construct(EditQuestion $questionEditor)
    {
        $this->questionEditor=$questionEditor;
    }

    /**
     * @Route("/admin/edit/question/{id}")
     */
    public function index(string $id): Response
    {
        return $this->redirectToRoute('edit_question', ['id'=>$id]);
    }

    /**
     * @Route("/admin/{_locale<%app.supported_locales%>}/edit/question/{id}", name="edit_question")
     * @param Request $request
     * @param QuestionRepository $questionRepository
     * @param AnswerRepository $answerRepository
     * @param string $id
     * @return Response
     */
    public function edit(Request $request,QuestionRepository $questionRepository, AnswerRepository $answerRepository, string $id): Response
    {
        $question = $questionRepository->findOneBy(['id' => $id]);
        if (!$question) {
            throw $this->createNotFoundException();
        }
        $rightAnswer = $answerRepository->findRightAnswer($question);
        if (count($rightAnswer) > 1) {
            $this->addFlash('error', $this::RIGHT_ANSWERS_COUNT_ERROR);
        }
        $rightAnswerPosition = array_search($rightAnswer[0], $question->getAnswers()->toArray());
        return $this->processRequest($request, $question, $rightAnswerPosition);
    }

    /**
     * @Route("/admin/new/question")
     */
    public function noLocaleNewQuestion(): Response
    {
        return $this->redirectToRoute('new_question');
    }

    /**
     * @Route("/admin/{_locale<%app.supported_locales%>}/new/question", name="new_question")
     * @param Request $request
     * @return Response
     */
    public function newQuestion(Request $request): Response
    {
        $question=new Question();
        return $this->processRequest($request, $question);
    }

    private function processRequest(Request $request, Question $question, int $rightAnswerPosition=null): Response
    {
        $form = $this->createForm(EditQuestionType::class, $question);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $newRightAnswerPosition = intval($request->request->get('isTrue'));
            if (!$form->isValid()) {
                $rightAnswerPosition = $newRightAnswerPosition;
            } else {
                $this->questionEditor->changeQuestion($form->getData(), $newRightAnswerPosition, $this->getDoctrine()->getManager());
                return $this->redirectToRoute('question_editor');
            }
        }
        return $this->render('edit_question/index.html.twig', [
            'form' => $form->createView(), 'rightAnswerPosition' => $rightAnswerPosition,
        ]);
    }
}
