<?php

namespace App\Controller;

use App\Form\RestorePasswordType;
use App\Repository\UserRepository;
use App\Service\UserEditor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class RestorePasswordController extends AbstractController
{
    /**
     * @Route("/{_locale<%app.supported_locales%>}/restore/password/{code}", name="restore_password")
     * @param Request $request
     * @param UserEditor $userEditor
     * @param UserRepository $userRepository
     * @param string $code
     * @return RedirectResponse|Response
     */
    public function index(Request $request, UserEditor $userEditor, UserRepository $userRepository, string $code)
    {
        $user = $userRepository->findOneBy(['confirmationCode' => $code]);
        if (!$user) {
            throw $this->createNotFoundException();
        }
        $form = $this->createForm(RestorePasswordType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $userEditor->encodeAndSetPassword($form->getData());
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('app_login');
        }
        return $this->render('restore_password/index.html.twig', ['form' => $form->createView()]);
    }
}
