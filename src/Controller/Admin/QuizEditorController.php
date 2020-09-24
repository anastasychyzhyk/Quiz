<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Repository\QuizRepository;
use App\Service\AdminGridEditor;
use App\Service\GroupOperations\QuizGroupOperations;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class QuizEditorController extends AbstractController
{
    /**
     * @Route("/admin/quiz/editor")
     */
    public function index(): Response
    {
        return $this->redirectToRoute('quiz_editor');
    }

    /**
     * @Route("/admin/{_locale<%app.supported_locales%>}/quiz/editor", name="quiz_editor")
     * @param Request $request
     * @param QuizRepository $quizRepository
     * @param QuizGroupOperations $quizGroupOperations
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function quizzes(
        Request $request,
        QuizRepository $quizRepository,
        QuizGroupOperations $quizGroupOperations,
        PaginatorInterface $paginator
    ): Response
    {
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);
        $adminGridEditor = new AdminGridEditor(
            $request,
            $quizGroupOperations,
            $quizRepository,
            $this->getDoctrine()->getManager()
        );
        if ($form->isSubmitted()) {
            $adminGridEditor->processRequest();
        }
        $pagination = $adminGridEditor->getPagination($paginator);
        return $this->render('quiz_editor/index.html.twig', ['form' => $form->createView(),
            'pagination' => $pagination
        ]);
    }
}
