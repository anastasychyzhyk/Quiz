<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\User;

class HomeController extends AbstractController
{
    /**
     * @Route("/{_locale<%app.supported_locales%>}/", name="home")
     * @return Response
     */
    public function home(): Response
    {
        $errorMessages = [User::USER_STATUS_BLOCKED=>'Sorry, you are blocked', User::USER_STATUS_AWAITING=>'Your account is not confirmed. Please check email'];
        if(($this->getUser()) && ($this->getUser()->getStatus()!==User::USER_STATUS_ACTIVE)) {
            $this->addFlash('error', $errorMessages[$this->getUser()->getStatus()]);
        }
        if(($this->getUser()) && ($this->getUser()->getRole()===User::ROLE_ADMIN)) {
            return $this->redirectToRoute('admin');
        }
        return $this->render('home/home.html.twig',[
            'controller_name'=>'HomeController'
        ]);
    }
	
	 /**
     * @Route("/")
     */
    public function index(): Response
    {
         return $this->redirectToRoute('home');
    }
}
