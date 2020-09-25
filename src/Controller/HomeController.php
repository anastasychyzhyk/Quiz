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
        if (($this->getUser()) && ($this->getUser()->getStatus()!==User::USER_STATUS_ACTIVE)) {
            $this->addFlash('error', $errorMessages[$this->getUser()->getStatus()]);
        }
        return $this->render('home/home.html.twig', [
            'controller_name'=>'HomeController'
        ]);
    }

    public static function checkAccess(AbstractController $controller)
    {
        $errorMessages = [User::USER_STATUS_BLOCKED=>'Sorry, you are blocked', User::USER_STATUS_AWAITING=>'Your account is not confirmed. Please check email'];
        if (($controller->getUser()) && ($controller->getUser()->getStatus()!==User::USER_STATUS_ACTIVE)) {
            $controller->addFlash('error', $errorMessages[$controller->getUser()->getStatus()]);
            throw $controller->createNotFoundException();
        }
    }
    
    /**
    * @Route("/")
    */
    public function index(): Response
    {
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/home_ru", name="home_ru")
     */
    public function homeRu(): Response
    {
        return $this->redirectToRoute('home', ['_locale'=>"ru"]);
    }

    /**
     * @Route("/home_en", name="home_en")
     */
    public function homeEn(): Response
    {
        return $this->redirectToRoute('home', ['_locale'=>"en"]);
    }
}
