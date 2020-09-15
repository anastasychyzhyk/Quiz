<?php

namespace App\Controller;

use App\Form\ForgetPasswordType;
use App\Repository\UserRepository;
use App\Service\Mailer;
use App\Service\UserEditor;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class ForgetPasswordController extends AbstractController
{
    /**
     * @Route("/{_locale<%app.supported_locales%>}/forget/password", name="forget_password")
     * @param Request $request
     * @param UserEditor $userEditor
     * @param Mailer $mailer
     * @param AuthenticationUtils $authenticationUtils
     * @param UserRepository $userRepository
     * @return Response
     */
    public function index(Request $request, UserEditor $userEditor, Mailer $mailer, AuthenticationUtils $authenticationUtils, UserRepository $userRepository)
    {
        $form = $this->createForm(ForgetPasswordType::class);
        $form->handleRequest($request);
        $lastUsername = $authenticationUtils->getLastUsername();
        $error = false;
        if ($form->isSubmitted()) {
            $user = $userRepository->findOneBy(['email' => $request->request->get('email')]);
            if ($user !== null) {
                $user = $userEditor->setConfirmationCode($user, $this->getDoctrine()->getManager());
                $this->addFlash('notice', $mailer->sendRestorePasswordMessage('Restore password', $user));
                return $this->redirectToRoute('home');
            } else {
                $error = true;
            }
        }
        return $this->render('forget_password/index.html.twig', ['form' => $form->createView(), 'error' => $error, 'last_username' => $lastUsername
        ]);
    }

    /**
     * @Route("/forget/password")
     */
    public function forgetPassword()
    {
        return $this->redirectToRoute('forget_password');
    }
}
