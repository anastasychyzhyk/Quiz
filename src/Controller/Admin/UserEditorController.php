<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserEditorType;
use App\Repository\UserRepository;
use App\Service\AdminGridEditor;
use App\Service\GroupOperations\UserGroupOperations;
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
     * @param UserGroupOperations $userGroupOperations
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function index(Request $request, UserRepository $userRepository,  UserGroupOperations $userGroupOperations,
                          PaginatorInterface $paginator): Response
    {
        $form = $this->createForm(UserEditorType::class);
        $form->handleRequest($request);
        $adminGridEditor = new AdminGridEditor($request, $userGroupOperations, $userRepository,
            $this->getDoctrine()->getManager());
        if ($form->isSubmitted()) {
            $adminGridEditor->processRequest();
        }
        $pagination = $adminGridEditor->getPagination($paginator);
        return $this->render('user_editor/index.html.twig', ['form' => $form->createView(),
            'pagination' => $pagination, 'roles' => User::getRolesArray(), 'statuses' => User::getStatusArray()
        ]);
    }
}