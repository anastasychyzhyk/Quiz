<?php
declare(strict_types=1);

namespace App\Controller;

use App\Form\QuestionEditorType;
use App\Service\AdminGridEditor;
use App\Service\QuestionEditor;
use App\Repository\QuestionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

class QuestionEditorController extends AbstractController
{
    /**
     * @Route("/question/editor")
     */
    public function index(): Response
    {
        return $this->redirectToRoute('question_editor');
    }

    /**
     * @Route("/{_locale<%app.supported_locales%>}/question/editor", name="question_editor")
     * @param Request $request
     * @param QuestionRepository $questionRepository
     * @param QuestionEditor $questionEditor
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function questionEditor(Request $request, QuestionRepository $questionRepository,  QuestionEditor $questionEditor,
                          PaginatorInterface $paginator): Response
    {
        $form = $this->createForm(QuestionEditorType::class);
        $form->handleRequest($request);
        $processedOperations = array('deleteEntity');
        $adminGridEditor = new AdminGridEditor($request, $questionEditor, $questionRepository, $processedOperations, $this->getDoctrine()->getManager());
        if($form->isSubmitted()) {
            $searchedText=$adminGridEditor->processRequest();
         }
        $pagination= $adminGridEditor->getPagination($searchedText??'', $paginator);
        return $this->render('question_editor/index.html.twig', ['form' => $form->createView(),
            'pagination' => $pagination,
        ]);
    }


}