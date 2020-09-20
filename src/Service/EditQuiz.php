<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Quiz;
use App\Repository\QuestionRepository;
use Doctrine\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

class EditQuiz
{
    private QuestionRepository $questionRepository;
    private PaginatorInterface $paginator;

    public function __construct(QuestionRepository $questionRepository, PaginatorInterface $paginator)
    {
        $this->questionRepository = $questionRepository;
        $this->paginator = $paginator;
    }

    public function processRequest(Request $request, Quiz $quiz, ObjectManager $entityManager): Quiz
    {
        if ($request->request->get('addQuestion') !== null) {
            $quiz = $this->addQuestion($request, $quiz, $entityManager);
        } else if ($request->request->get('deleteQuestion') !== null) {
            $this->deleteQuestion($request, $quiz, $entityManager);
        }
        return $quiz;
    }

    private function deleteQuestion(Request $request, Quiz $quiz, ObjectManager $entityManager)
    {
        $selectedItems = $request->request->get('checkbox');
        if ($selectedItems !== null) {
            foreach ($selectedItems as $selectedItem) {
                $question = $this->questionRepository->findOneBy(['id' => $selectedItem]);
                $quiz->getQuestion()->removeElement($question);
                $question->getQuizzes()->removeElement($quiz);
            }
            $entityManager->flush();
        }
    }

    private function addQuestion(Request $request, Quiz $quiz, ObjectManager $entityManager): Quiz
    {
        $question = $this->questionRepository->findOneBy(['id' => $request->request->get('addQuestion')]);
        $quiz = $quiz->addQuestion($question);
        $question->addQuiz($quiz);
        $entityManager->flush();
        return $quiz;
    }

    public function getPaginations(Request $request, Quiz $quiz): array
    {
        $query = $this->questionRepository->findByTextQuery("");
        $paginationFind = $this->paginator->paginate($query, $request->query->getInt('page', 1), 10);
        $query = $this->questionRepository->findByQuizQuery($quiz);
        $pagination = $this->paginator->paginate($query, $request->query->getInt('page', 1), 20);
        return ['paginationFind'=>$paginationFind, 'pagination'=>$pagination ];
    }
}