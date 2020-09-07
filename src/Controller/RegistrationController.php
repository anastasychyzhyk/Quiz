<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Form\RegistrationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Service\UserEditor;
use App\Service\Mailer;

class RegistrationController extends AbstractController
{
    private UserEditor $userEditor;
    private const EMAIL_INPUT_ERROR='Please check your email. User with this email is already registered.';
    private const EMAIL_SEND_ERROR='An error occurred during sending confirmation email. Please contact support.';
    private const INVALID_CONFIRMATION='Invalid confirmation code';
    private const CONFIRM_SUCCESS='Account verified successfully';

    public function __construct(UserEditor $userEditor)
    {
        $this->userEditor=$userEditor;
    }

    /**
     * @Route("/{_locale<%app.supported_locales%>}/registration", name="registration")
     */
    public function index(Request $request, Mailer $mailer)
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user=$form->getData();
            $user=$this->userEditor->createUser($user, $this->getDoctrine()->getManager());
            if (!$user) {
                $this->addFlash('error', self::EMAIL_INPUT_ERROR);
            }
            else if (!$mailer->sendConfirmationMessage('Confirm registration', $user)) {
                $this->addFlash('error', self::EMAIL_SEND_ERROR);
            }
        }
        return $this->render('registration/index.html.twig', [
            'controller_name' => 'RegistrationController', 'form' => $form->createView()
        ]);
    }
	
	/**
     * @Route("/{_locale<%app.supported_locales%>}/confirmation/{code}", name="confirmation")
     */
    public function confirm(Request $request, UserRepository $userRepository, string $code)
    {
        $user=$userRepository->findOneBy(['confirmationCode' => $code]);
		if($user) {
            $user->activate();
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('error', self::CONFIRM_SUCCESS);
        }
		else {
            $this->addFlash( 'error',self::INVALID_CONFIRMATION);
        }
        return $this->redirectToRoute('home');
	}
}
