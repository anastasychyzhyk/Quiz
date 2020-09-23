<?php

namespace App\Controller;

use App\Form\QuizType;
use App\Repository\PlayRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuizFinishController extends AbstractController
{
    /**
     * @Route("/{_locale<%app.supported_locales%>}/quiz/finish/{id}", name="quiz_finish")
     * @param Request $request
     * @param PlayRepository $playRepository
     * @param string $id
     * @return Response
     */
    public function index(
        Request $request,
        PlayRepository $playRepository, string $id
    ): Response
    {
        $form = $this->createForm(QuizType::class);
        $form->handleRequest($request);
        $results = $playRepository->findResults($id);
        if ($results == null) {
            throw $this->createNotFoundException();
        }
        $userResult = $playRepository->findUserResult($id, $this->getUser()->getId());

        return $this->render('quiz_finish/index.html.twig', ['form' => $form->createView(),
            'results' => $results, 'userResult' => $userResult[0]
        ]);
    }
}
