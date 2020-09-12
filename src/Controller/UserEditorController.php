<?php
declare(strict_types=1);

namespace App\Controller;

use App\Form\UserEditorType;
use App\Repository\UserRepository;
use App\Service\AdminGridEditor;
use App\Service\UserEditor;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserEditorController extends AbstractController
{
    /**
     * @Route("/user/editor")
     */
    public function userEditor(): Response
    {
        return $this->redirectToRoute('user_editor');
    }

    /**
     * @Route("/{_locale<%app.supported_locales%>}/user/editor", name="user_editor")
     * @param Request $request
     * @param UserRepository $userRepository
     * @param UserEditor $userEditor
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function index(Request $request, UserRepository $userRepository,  UserEditor $userEditor, PaginatorInterface $paginator): Response
    {
        $form = $this->createForm(UserEditorType::class);
        $form->handleRequest($request);
        $processedOperations = array('activateUsers', 'blockUsers', 'setUser', 'setAdmin', 'deleteEntity');
        $adminGridEditor = new AdminGridEditor($request, $userEditor, $userRepository, $processedOperations, $this->getDoctrine()->getManager());
        if ($form->isSubmitted()) {
            $searchedText = $adminGridEditor->processRequest();
        }
        $pagination = $adminGridEditor->getPagination($searchedText ?? '', $paginator);
        return $this->render('user_editor/index.html.twig', ['form' => $form->createView(),
            'pagination' => $pagination,
        ]);
    }
}