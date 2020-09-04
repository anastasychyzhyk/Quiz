<?php

namespace App\Controller;

use App\Entity\Question;
use App\Form\QuestionEditorType;
use App\Service\QuestionEditor;
use App\Repository\QuestionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

class QuestionEditorController extends AbstractController
{
    private QuestionRepository $questionRepository;
    public function __construct(QuestionRepository $questionRepository)
    {
        $this->questionRepository=$questionRepository;
    }

    /**
     * @Route("/{_locale<%app.supported_locales%>}/question/editor", name="question_editor")
     */
    public function index(Request $request, PaginatorInterface $paginator, QuestionEditor $questionEditor)
    {
        $form = $this->createForm(QuestionEditorType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if($request->request->get('find')!== null) {
                $questions=$this->questionRepository->findByTextQuery($request->request->get('findText'));
            }
            if($request->request->get('delete')!== null) {
                $questionEditor->deleteQuestion($request->request->get('checkbox'), $this->questionRepository, $this->getDoctrine()->getManager());
                $questions=$this->questionRepository->findQuery();
            }
        }
        else {
            $questions=$this->questionRepository->findQuery();
        }
        $pagination = $paginator->paginate($questions, $request->query->getInt('page', 1), 20);
        return $this->render('question_editor/index.html.twig', ['form' => $form->createView(),
            'pagination' => $pagination,
        ]);
    }
}
