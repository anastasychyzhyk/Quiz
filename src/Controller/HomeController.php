<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

class HomeController extends AbstractController
{
    /**
     * @Route("/{_locale<%app.supported_locales%>}/", name="home")
     */
    public function home(): Response
    {
        return $this->render('home/home.html.twig',[
            'controller_name'=>'HomeController'
        ]);
    }
}
