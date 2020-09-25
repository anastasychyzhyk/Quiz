<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\HomeController;
use App\Entity\Quiz;
use App\Form\EditQuizType;
use App\Repository\QuestionRepository;
use App\Repository\QuizRepository;
use App\Service\EditQuiz;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EditQuizController extends AbstractController
{
    private QuestionRepository $questionRepository;
    private PaginatorInterface $paginator;
    private EditQuiz $quizEditor;

    public function __construct(QuestionRepository $questionRepository, PaginatorInterface $paginator, EditQuiz $quizEditor)
    {
        $this->questionRepository = $questionRepository;
        $this->paginator = $paginator;
        $this->quizEditor = $quizEditor;
    }

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
    public function edit(Request $request, QuizRepository $quizRepository, string $id): Response
    {
        HomeController::checkAccess($this);
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
        HomeController::checkAccess($this);
        $quiz = new Quiz();
        $this->getDoctrine()->getManager()->persist($quiz);
        return $this->processRequest($request, $quiz);
    }

    private function processRequest(Request $request, Quiz $quiz): Response
    {
        $form = $this->createForm(EditQuizType::class, $quiz);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            if ($request->request->get('save') !== null) {
                $entityManager->persist($quiz);
                $entityManager->flush();
                return $this->redirectToRoute('quiz_editor');
            } else {
                $quiz = $this->quizEditor->processRequest($request, $quiz, $entityManager);
            }
        }
        $questionLists = $this->quizEditor->getQuestionLists($request, $quiz);
        return $this->render('edit_quiz/index.html.twig', ['questions' => $quiz->getQuestion(),
            'form' => $form->createView(), 'pagination' => $questionLists['pagination'],
            'questionsFind' => $questionLists['questionsFind']
        ]);
    }

    /**
     * @Route("/find/questions", name="find_questions")
     */
    public function findQuestions(): JsonResponse
    {
        $text = $_POST['searchedText'];
        $questions = $this->questionRepository->findByTextQuery($text, 10)->getResult();
        $idx = 0;
        $jsonData = array();
        foreach ($questions as $question) {
            $temp = array(
                'id' => $question->getId(),
                'text' => $question->getText(),
            );
            $jsonData[$idx++] = $temp;
        }
        return new JsonResponse($jsonData);
    }
}
