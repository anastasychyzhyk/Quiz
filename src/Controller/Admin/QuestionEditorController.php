<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\HomeController;
use App\Form\Filters\FindEditorFilter;
use App\Service\PaginationWithFilter;
use App\Repository\QuestionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

class QuestionEditorController extends AbstractController
{
    /**
     * @Route("/admin/question/editor")
     */
    public function index(): Response
    {
        return $this->redirectToRoute('question_editor');
    }

    /**
     * @Route("/admin/{_locale<%app.supported_locales%>}/question/editor", name="question_editor")
     * @param Request $request
     * @param QuestionRepository $questionRepository
     * @param PaginatorInterface $paginator
     * @param PaginationWithFilter $paginationWithFilter
     * @return Response
     */
    public function questionEditor(
        Request $request,
        QuestionRepository $questionRepository,
        PaginatorInterface $paginator,
        PaginationWithFilter $paginationWithFilter
    ): Response
    {
        HomeController::checkAccess($this);
        $form = $this->createForm(FindEditorFilter::class);
        $form->handleRequest($request);
        $pagination = $paginationWithFilter->getPagination($paginator, $form, $request, $questionRepository);
        return $this->render('question_editor/index.html.twig', ['form' => $form->createView(),
            'pagination' => $pagination,
        ]);
    }
}
