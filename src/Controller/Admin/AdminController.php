<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\HomeController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin")
     */
    public function index(): Response
    {
        return $this->redirectToRoute('admin');
    }

    /**
     * @Route("/admin/{_locale<%app.supported_locales%>}/", name="admin")
     * @param Request $request
     * @return Response
     */
    public function edit(Request $request): Response
    {
        HomeController::checkAccess($this);
        return $this->render('admin/index.html.twig');
    }
}
