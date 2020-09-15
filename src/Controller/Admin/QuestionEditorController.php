<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Form\QuestionEditorType;
use App\Service\AdminGridEditor;
use App\Repository\QuestionRepository;
use App\Service\GroupOperations\QuestionGroupOperations;
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
     * @param QuestionGroupOperations $questionGroupOperations
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function questionEditor(Request $request, QuestionRepository $questionRepository,
                                   QuestionGroupOperations $questionGroupOperations, PaginatorInterface $paginator): Response
    {
        $form = $this->createForm(QuestionEditorType::class);
        $form->handleRequest($request);
        $adminGridEditor = new AdminGridEditor($request, $questionGroupOperations, $questionRepository,
            $this->getDoctrine()->getManager());
        if ($form->isSubmitted()) {
            $adminGridEditor->processRequest();
        }
        $pagination = $adminGridEditor->getPagination($paginator);
        return $this->render('question_editor/index.html.twig', ['form' => $form->createView(),
            'pagination' => $pagination
        ]);
    }
}