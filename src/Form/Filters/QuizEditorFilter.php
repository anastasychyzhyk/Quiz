<?php
declare(strict_types=1);

namespace App\Form\Filters;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class QuizEditorFilter extends FindEditorFilter
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('isActive', ChoiceType::class, [
                'choices' => [
                    'Active' => '1',
                    'Stop' => '0',
                    'All' => '',
                ],
            ]);
    }
}