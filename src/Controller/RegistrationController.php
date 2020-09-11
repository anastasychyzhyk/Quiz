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
use App\Service\UserEditor;
use App\Service\Mailer;

class RegistrationController extends AbstractController
{
    private UserEditor $userEditor;
    private const EMAIL_SEND_ERROR='An error occurred during sending confirmation email. Please contact support.';
	private const CONFIRM_SENDED='Congratulate with successfull registration! Please check your email and confirm accont.';
    private const INVALID_CONFIRMATION='Invalid confirmation code';
    private const CONFIRM_SUCCESS='Account verified successfully';
	

    public function __construct(UserEditor $userEditor)
    {
        $this->userEditor=$userEditor;
    }

    /**
     * @Route("/{_locale<%app.supported_locales%>}/registration", name="registration")
     * @param Request $request
     * @param Mailer $mailer
     * @return Response
     */
    public function index(Request $request, Mailer $mailer): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user=$form->getData();
            $user=$this->userEditor->createUser($user, $this->getDoctrine()->getManager());
            if (!$mailer->sendConfirmationMessage('Confirm registration', $user)) {
                $this->addFlash('error', self::EMAIL_SEND_ERROR);
            }
            else {
                $this->addFlash('notice', self::CONFIRM_SENDED);
            }
            return $this->redirectToRoute('home');
        }
        return $this->render('registration/index.html.twig', [
            'controller_name' => 'RegistrationController', 'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{_locale<%app.supported_locales%>}/confirmation/{code}", name="confirmation")
     * @param Request $request
     * @param UserRepository $userRepository
     * @param string $code
     * @return Response
     */
    public function confirm(Request $request, UserRepository $userRepository, string $code): Response
    {
        $user=$userRepository->findOneBy(['confirmationCode' => $code]);
		if($user) {
            $user->activate();
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('notice', self::CONFIRM_SUCCESS);
        }
		else {
            $this->addFlash( 'error',self::INVALID_CONFIRMATION);
        }
        return $this->redirectToRoute('home');
	}
}
