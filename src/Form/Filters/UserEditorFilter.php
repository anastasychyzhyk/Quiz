<?php
declare(strict_types=1);

namespace App\Form\Filters;

use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class UserEditorFilter extends FindEditorFilter
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('role', ChoiceType::class, [
                'choices' => [
                    'User' => User::ROLE_USER,
                    'Administrator' => USER::ROLE_ADMIN,
                    'All' => '',
                ],
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'User.Active' => User::USER_STATUS_ACTIVE,
                    'User.Blocked' => USER::USER_STATUS_BLOCKED,
                    'User.Awaiting' => USER::USER_STATUS_AWAITING,
                    'All' => '',
                ],
            ]);
    }
}