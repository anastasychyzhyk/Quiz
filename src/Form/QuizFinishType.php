<?php

namespace App\Form;

use App\Entity\Play;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuizFinishType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('rightAnswersCount')
            ->add('isFinish')
            ->add('time')
            ->add('user')
            ->add('quiz')
            ->add('question')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Play::class,
        ]);
    }
}
