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
    private UserRepository $userRepository;
    private UserEditor $userEditor;
    private PaginatorInterface $paginator;
    private AdminGridEditor $adminGridEditor;

    public function __construct(UserRepository $userRepository,  UserEditor $userEditor,
                                PaginatorInterface $paginator, AdminGridEditor $adminGridEditor)
    {
        $this->userRepository=$userRepository;
        $this->userEditor=$userEditor;
        $this->paginator=$paginator;
        $this->adminGridEditor=$adminGridEditor;
    }

    /**
     * @Route("/{_locale<%app.supported_locales%>}/user/editor", name="user_editor")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $form = $this->createForm(UserEditorType::class);
        $form->handleRequest($request);
        if($form->isSubmitted()) {
            $searchedText=$this->adminGridEditor->getSearchedText($request);
            $this->adminGridEditor->processDelete($request, $this->userEditor, $this->getDoctrine()->getManager());
        }
        $pagination= $this->adminGridEditor->getPagination($this->userRepository, $searchedText??'', $request, $this->paginator);
        return $this->render('user_editor/index.html.twig', ['form' => $form->createView(),
            'pagination' => $pagination,
        ]);
    }

}