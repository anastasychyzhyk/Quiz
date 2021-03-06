<?php

namespace App\Controller;

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
        HomeController::checkAccess($this);
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);
        $results = $playRepository->findResults($id);
        if ($results == null) {
            throw $this->createNotFoundException();
        }
        $userPosition=array_search($this->getUser()->getId(), array_column($results, 'id'));
        return $this->render('quiz_finish/index.html.twig', ['form' => $form->createView(),
            'results' => $results, 'userPosition' => $userPosition
        ]);
    }
}
