<?php
declare(strict_types=1);

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\Security\Core\Security;
use App\Entity\User;

final class MenuBuilder
{
    private $factory;
    private $securityContext;

    public function __construct(FactoryInterface $factory, Security $securityContext)
    {
        $this->factory = $factory;
        $this->securityContext = $securityContext;
    }

    public function createMainMenu(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');
        $menu->addChild('Home', ['route' => 'home']);
        if ($this->securityContext->getUser()) {
            if ($this->securityContext->getUser()->getRole() == User::ROLE_ADMIN) {
                $menu->addChild('Administration', ['route' => 'admin']);
            }
            $menu->addChild('Logout', ['route' => 'app_logout']);
        } else {
            $menu->addChild('Sign in', ['route' => 'app_login']);
            $menu->addChild('Sign up', ['route' => 'registration']);
        }
        $menu->addChild('Language')
            ->setAttribute('dropdown', true);
        $menu['Language']->addChild('English', ['route' => 'home_en'])
            ->setAttribute('is_in_dropdown', true);
        $menu['Language']->addChild('Russian', ['route' => 'home_ru'])
            ->setAttribute('is_in_dropdown', true);

        return $menu;
    }
}