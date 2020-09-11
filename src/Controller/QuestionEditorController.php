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
    private QuestionRepository $questionRepository;
    private QuestionEditor $questionEditor;
    private PaginatorInterface $paginator;
    private AdminGridEditor $adminGridEditor;

    public function __construct(QuestionRepository $questionRepository,  QuestionEditor $questionEditor,
                                PaginatorInterface $paginator, AdminGridEditor $adminGridEditor)
    {
        $this->questionRepository=$questionRepository;
        $this->questionEditor=$questionEditor;
        $this->paginator=$paginator;
        $this->adminGridEditor=$adminGridEditor;
    }

    /**
     * @Route("/{_locale<%app.supported_locales%>}/question/editor", name="question_editor")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $form = $this->createForm(QuestionEditorType::class);
        $form->handleRequest($request);
        if($form->isSubmitted()) {
            $searchedText=$this->adminGridEditor->getSearchedText($request);
            $this->adminGridEditor->processDelete($request, $this->questionEditor, $this->getDoctrine()->getManager());
         }
        $pagination= $this->adminGridEditor->getPagination($this->questionRepository, $searchedText??'', $request, $this->paginator);
        return $this->render('question_editor/index.html.twig', ['form' => $form->createView(),
            'pagination' => $pagination,
        ]);
    }


}