<?php
declare(strict_types=1);

namespace App\Form\Filters;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class FindEditorFilter extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('searchedText', TextType::class, ['label' => false, 'required' => false])
            ->setMethod('GET')->getForm();
    }
}