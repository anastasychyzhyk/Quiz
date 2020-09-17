<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin")
     */
    public function index()
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
        return $this->render('admin/index.html.twig');
    }
}
