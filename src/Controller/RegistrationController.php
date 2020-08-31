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
        $errorMessage = '';
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user=$form->getData();
            $user=$this->userEditor->createUser($user, $this->getDoctrine()->getManager());
            if (!$user) {
                $errorMessage = 'Please check your email or login. User with this email is already registered.';
            }
            else if (!$mailer->sendConfirmationMessage('Confirm registration', $user)) {
                $errorMessage = 'An error occurred during sending confirmation email. Please contact support.';
            }
        }
        return $this->render('registration/index.html.twig', [
            'controller_name' => 'RegistrationController', 'form' => $form->createView(), 'message' => $errorMessage,
        ]);
    }
	
	/**
     * @Route("/confirmation/{code}", name="confirmation")
     */
    public function confirm(Request $request, UserRepository $userRepository, string $code)
    {
        $user=$userRepository->findOneBy(['confirmationCode' => $code]);
		if($user) {
            $user->activate();
            $this->getDoctrine()->getManager()->flush();
        }
		else {
		    echo 'Invalid confirmation code';
        }
        return $this->render('base.html.twig');
	}
}
