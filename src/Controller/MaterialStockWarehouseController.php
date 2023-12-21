<?php

namespace App\Controller;

use App\Repository\MaterialRepository;
use App\Repository\MaterialStockWarehouseRepository;
use App\Repository\UserRepository;
use App\Repository\WarehouseRepository;
use App\Service\StockService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MaterialStockWarehouseController extends AbstractController
{
    public function __construct(public StockService $stockService)
    {
    }

    #[Route('/api/stock', name: 'add-stock', methods: 'POST')]
    public function addStock(Request $request): JsonResponse
    {
        $materialToBeAddedToStock = $this->stockService->createNewStock(json_decode($request->getContent(), true));
    }
    #[Route('/api/stock/edit/{warehouseId}/{materialId}/{userId}', name: 'update-stock', methods: 'PATCH')]
    public function edit(int $warehouseId, int $materialId, int $userId, Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(),true);

        $materialStock = $this->stockService->getMaterialStock($warehouseId,$materialId,$userId);

        if (Response::HTTP_NOT_FOUND === $materialStock['status']) {
            return new JsonResponse([
                'message' => $materialStock['message'],
            ], Response::HTTP_NOT_FOUND);
        }

        $updatedMaterialStock = $this->stockService->updateCurrentStock(
            $materialStock['data'],
            $requestData['quantity']
        );

        if (Response::HTTP_BAD_REQUEST === $updatedMaterialStock['status']) {
            return new JsonResponse([
                'message' => $updatedMaterialStock['message'],
            ], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse([
            'message' => 'Stock updated successfully.',
        ], Response::HTTP_CREATED);
    }

    #[Route('/api/stock/delete/{warehouseId}/{materialId}/{userId}', name: 'delete-stock', methods: 'DELETE')]
    public function delete(int $warehouseId, int $materialId, int $userId): JsonResponse
    {

        $materialStock = $this->stockService->getMaterialStock($warehouseId,$materialId,$userId);

        if (Response::HTTP_NOT_FOUND === $materialStock['status']) {
            return new JsonResponse([
                'message' => $materialStock['message'],
            ], Response::HTTP_NOT_FOUND);
        }

        $this->stockService->deleteStock($materialStock['data']);

        return new JsonResponse([
            'message' => 'Material deleted successfully',
        ], Response::HTTP_FOUND);
    }

}
