<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Question;
use App\Form\EditQuestionType;
use App\Repository\QuestionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class EditQuestionController extends AbstractController
{
     /**
     * @Route("/{_locale<%app.supported_locales%>}/edit/question/{id}", name="edit_question")
     * @param int $id
     * @return Response
     */
    public function edit(Request $request,QuestionRepository $questionRepository, int $id)
    {
        $question = $questionRepository->findOneBy(['id'=>$id]);
        $form = $this->createForm(EditQuestionType::class, $question);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $question=$form->getData();
            if($request->request->get('add')!== null) {
                $answer = new Answer();
                $question->getAnswers()->add($answer);
            }
            if($request->request->get('delete')!== null) {
                $question->getAnswers()->array_search($answer);
                var_dump($request->request->get('add'));
            }
            /*if ($questionRepository->findOneBy(['text' => $question->getText()])) {
                $this->addFlash('error', '1111111');
            }
            else {
                $this->getDoctrine()->getManager()->persist($question);
                $this->getDoctrine()->getManager()->flush();
            }*/
        }
        return $this->render('edit_question/index.html.twig', [
            'controller_name' => 'EditQuestionController','form' => $form->createView(), 'answers'=>$question->getAnswers()
        ]);

        return $this->render('edit_question/index.html.twig', [
            'controller_name' => 'EditQuestionController',
        ]);
    }

    /**
     * @Route("/{_locale<%app.supported_locales%>}/new/question", name="new_question")
     * @param Request $request
     * @param QuestionRepository $questionRepository
     * @return Response
     */
    public function newQuestion(Request $request, QuestionRepository $questionRepository)
    {
        $question = new Question();
        for($i=1; $i<3; $i++) {
            $answer = new Answer();
            var_dump($answer->getId());
            $question->getAnswers()->add($answer);
        }
        $form = $this->createForm(EditQuestionType::class, $question);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $question=$form->getData();
            if($request->request->get('add')!== null) {
                $answer = new Answer();
                $question->getAnswers()->add($answer);
            }
            if($request->request->get('delete')!== null) {
                $question->getAnswers()->array_search($answer);
                var_dump($request->request->get('add'));
            }
            /*if ($questionRepository->findOneBy(['text' => $question->getText()])) {
                $this->addFlash('error', '1111111');
            }
            else {
                $this->getDoctrine()->getManager()->persist($question);
                $this->getDoctrine()->getManager()->flush();
            }*/
        }
        return $this->render('edit_question/index.html.twig', [
            'controller_name' => 'EditQuestionController','form' => $form->createView(), 'answers'=>$question->getAnswers()
        ]);
    }
}
