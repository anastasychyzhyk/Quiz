<?php
declare(strict_types=1);

namespace App\Service\GroupOperations;

use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;

abstract class GroupOperations
{
    protected array $processedOperations;

    public function __construct(array $processedOperations)
    {
        $this->processedOperations=$processedOperations;
    }

    public function processGroupOperation(Request $request, array $selectedItems, ObjectManager $entityManager): void
    {
        foreach ($request->request->keys() as $requestKey) {
            if (array_search($requestKey, $this->processedOperations) !== false) {
                foreach ($selectedItems as $selectedItem) {
                    $this->doOperation($requestKey, $request->request->get($requestKey), $selectedItem, $entityManager);
                }
            }
        }
        $entityManager->flush();
    }

    protected abstract function doOperation(string $requestKey, string $requestValue, string $selectedItem,
                                            ObjectManager $entityManager):void;
}