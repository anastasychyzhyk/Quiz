<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\HomeController;
use App\Entity\User;
use App\Form\Filters\UserEditorFilter;
use App\Repository\UserRepository;
use App\Service\PaginationWithFilter;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserEditorController extends AbstractController
{
    /**
     * @Route("/admin/user/editor")
     */
    public function userEditor(): Response
    {
        return $this->redirectToRoute('user_editor');
    }

    /**
     * @Route("/admin/{_locale<%app.supported_locales%>}/user/editor", name="user_editor")
     * @param Request $request
     * @param UserRepository $userRepository
     * @param PaginatorInterface $paginator
     * @param PaginationWithFilter $paginationWithFilter
     * @return Response
     */
    public function index(
        Request $request,
        UserRepository $userRepository,
        PaginatorInterface $paginator,
        PaginationWithFilter $paginationWithFilter
    ): Response
    {
        HomeController::checkAccess($this);
        $form = $this->createForm(UserEditorFilter::class);
        $form->handleRequest($request);
        $pagination = $paginationWithFilter->getPagination($paginator, $form, $request, $userRepository);
        return $this->render('user_editor/index.html.twig', ['form' => $form->createView(),
            'pagination' => $pagination, 'roles' => User::getRolesArray(), 'statuses' => User::getStatusArray()
        ]);
    }
}
