<?php

namespace App\Controller;

use App\Repository\QuizRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuizSelectorController extends AbstractController
{

    /**
     * @Route("/{_locale<%app.supported_locales%>}/quiz/selector", name="quiz_selector")
     * @param Request $request
     * @param QuizRepository $quizRepository
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function index(
        Request $request,
        QuizRepository $quizRepository,
        PaginatorInterface $paginator
    ): Response
    {
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);
        $query = $quizRepository->findWithQuestionsCount($request->request->get('searchedText') ?? '');
        $pagination = $paginator->paginate($query, $request->query->getInt('page', 1), 20);
        return $this->render('quiz_selector/index.html.twig', ['form' => $form->createView(),
            'pagination' => $pagination
        ]);
    }
}
