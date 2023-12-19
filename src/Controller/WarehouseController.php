<?php

namespace App\Controller;

use App\Repository\WarehouseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WarehouseController extends AbstractController
{
    #[Route('api/warehouse/{id}', name: 'show_warehouse', methods: 'GET')]
    public function index(WarehouseRepository $warehouseRepository, int $id)
    {
        $warehouse = $warehouseRepository->find($id);

        if (!$warehouse) {
            return new JsonResponse([
                'message' => 'Warehouse is not defined.',
            ],
                Response::HTTP_NOT_FOUND
            );
        }

        return new JsonResponse([
            'name' => $warehouse->getName(),
            'materials' => $warehouse->getMaterials(),
            'capacity' => $warehouse->getCapacity(),
            'currentNumberOfCapacity' => $warehouse->getCurrentNumberOfMaterials(),
        ], Response::HTTP_OK);

    }
}
