<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\HomeController;
use App\Form\Filters\QuizEditorFilter;
use App\Repository\QuizRepository;
use App\Service\PaginationWithFilter;
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
     * @param PaginatorInterface $paginator
     * @param PaginationWithFilter $paginationWithFilter
     * @return Response
     */
    public function quizzes(
        Request $request,
        QuizRepository $quizRepository,
        PaginatorInterface $paginator,
        PaginationWithFilter $paginationWithFilter
    ): Response
    {
        HomeController::checkAccess($this);
        $form = $this->createForm(QuizEditorFilter::class);
        $form->handleRequest($request);
        $pagination = $paginationWithFilter->getPagination($paginator, $form, $request, $quizRepository);
        return $this->render('quiz_editor/index.html.twig', ['form' => $form->createView(),
            'pagination' => $pagination
        ]);
    }
}
