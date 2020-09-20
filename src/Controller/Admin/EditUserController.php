<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Form\EditUserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Service\EditUser;

class EditUserController extends AbstractController
{
    private EditUser $userEditor;
    private UserRepository $userRepository;

    public function __construct(EditUser $userEditor, UserRepository $userRepository)
    {
        $this->userEditor=$userEditor;
        $this->userRepository=$userRepository;
    }

    /**
     * @Route("/admin/edit/user/{id}")
     * @param string $id
     * @return Response
     */
    public function registration(string $id): Response
    {
        return $this->redirectToRoute('edit_user', ['id'=>$id]);
    }

    /**
     * @Route("/admin/{_locale<%app.supported_locales%>}/edit/user/{id}", name="edit_user")
     * @param Request $request
     * @param string $id
     * @return Response
     */
    public function editUser(Request $request, string $id): Response
    {
        $user = $this->userRepository->findOneBy(['id' => $id]);
        if (!$user) {
            throw $this->createNotFoundException();
        }
        $form = $this->createForm(EditUserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('user_editor');
        }
        return $this->render('edit_user/index.html.twig', ['form' => $form->createView()]);
    }
}
