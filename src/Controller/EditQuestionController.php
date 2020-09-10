<?php
declare(strict_types=1);

namespace App\Controller;

use App\Repository\AnswerRepository;
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
    private const RIGHT_ANSWERS_COUNT_ERROR="Error occurred! More than 1 right answer";

    public function __construct(QuestionEditor $questionEditor)
    {
        $this->questionEditor=$questionEditor;
    }

    /**
     * @Route("/{_locale<%app.supported_locales%>}/edit/question/{id}", name="edit_question")
     * @param Request $request
     * @param QuestionRepository $questionRepository
     * @param AnswerRepository $answerRepository
     * @param int $id
     * @return Response
     */
    public function edit(Request $request,QuestionRepository $questionRepository, AnswerRepository $answerRepository, int $id): Response
    {
        $question = $questionRepository->findOneBy(['id'=>$id]);
        $rightAnswer=$answerRepository->findRightAnswer($question);
        if(count($rightAnswer)>1) {
            $this->addFlash('error', $this::RIGHT_ANSWERS_COUNT_ERROR);
        }
        $rightAnswerPosition=array_search($rightAnswer[0], $question->getAnswers()->toArray());
        return $this->processRequest($request, $question, $rightAnswerPosition);
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

    private function processRequest(Request $request, Question $question, int $rightAnswerPosition=null): Response
    {
        $form = $this->createForm(EditQuestionType::class, $question);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $newRightAnswerPosition = intval($request->request->get('isTrue'));
            if(!$form->isValid()) {
                $rightAnswerPosition=$newRightAnswerPosition;
            }
            else {
                $this->questionEditor->changeQuestion($form->getData(), $newRightAnswerPosition, $this->getDoctrine()->getManager());
                return $this->redirectToRoute('question_editor');
            }
        }
        return $this->render('edit_question/index.html.twig', [
            'form' => $form->createView(), 'rightAnswerPosition'=>$rightAnswerPosition,
        ]);
    }
}
