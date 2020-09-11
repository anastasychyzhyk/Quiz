<?php
declare(strict_types=1);

namespace App\Controller;

use App\Form\QuestionEditorType;
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

    public function __construct(QuestionRepository $questionRepository,  QuestionEditor $questionEditor)
    {
        $this->questionRepository=$questionRepository;
        $this->questionEditor=$questionEditor;
    }

    /**
     * @Route("/{_locale<%app.supported_locales%>}/question/editor", name="question_editor")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $form = $this->createForm(QuestionEditorType::class);
        $form->handleRequest($request);
        $condition='';
        if ($form->isSubmitted() && $form->isValid()) {
            $condition=$this->processRequest($request);
        }
        $questions=$this->questionRepository->findByTextQuery($condition);
        $pagination = $paginator->paginate($questions, $request->query->getInt('page', 1), 20);
        return $this->render('question_editor/index.html.twig', ['form' => $form->createView(),
            'pagination' => $pagination,
        ]);
    }

    private function processRequest(Request $request): ?string
    {
        if($request->request->get('find')!== null) {
            return $request->request->get('findText');
        }
        else if(($request->request->get('delete')!== null) && ($request->request->get('checkbox')!==null)) {
            $this->questionEditor->deleteQuestion($request->request->get('checkbox'), $this->questionRepository,
                $this->getDoctrine()->getManager());
            return '';
        }
    }
}
