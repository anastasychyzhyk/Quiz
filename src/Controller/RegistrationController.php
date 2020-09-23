<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Form\RegistrationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Service\EditUser;
use App\Service\Mailer;

class RegistrationController extends AbstractController
{
    private EditUser $userEditor;
    private UserRepository $userRepository;
    private Mailer $mailer;
    private const CONFIRM_SUCCESS='Account verified successfully';

    public function __construct(EditUser $userEditor, UserRepository $userRepository, Mailer $mailer)
    {
        $this->userEditor=$userEditor;
        $this->userRepository=$userRepository;
        $this->mailer=$mailer;
    }

    /**
     * @Route("/registration")
     */
    public function registration(): Response
    {
        return $this->redirectToRoute('registration');
    }

    /**
     * @Route("/{_locale<%app.supported_locales%>}/registration", name="registration")
     * @param Request $request
     * @param Mailer $mailer
     * @return Response
     */
    public function index(Request $request): Response
    {
        $form = $this->createForm(RegistrationType::class, new User());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user=$this->userEditor->registerUser($form->getData(), $this->getDoctrine()->getManager());
            $this->addFlash('notice', $this->mailer->sendConfirmationMessage('Confirm registration', $user));
            return $this->redirectToRoute('home');
        }
        return $this->render('registration/index.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/{_locale<%app.supported_locales%>}/confirmation/{code}", name="confirmation")
     * @param UserRepository $userRepository
     * @param string $code
     * @return Response
     */
    public function confirm(UserRepository $userRepository, string $code): Response
    {
        $user=$userRepository->findOneBy(['confirmationCode' => $code]);
        if ($user) {
            $user->activate();
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('notice', self::CONFIRM_SUCCESS);
        } else {
            throw $this->createNotFoundException();
        }
        return $this->redirectToRoute('home');
    }
}
