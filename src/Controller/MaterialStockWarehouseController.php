<?php

namespace App\Controller;

use App\Repository\MaterialRepository;
use App\Repository\MaterialStockWarehouseRepository;
use App\Repository\UserRepository;
use App\Repository\WarehouseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MaterialStockWarehouseController extends AbstractController
{
    public function __construct(public MaterialRepository $materialRepository, public WarehouseRepository $warehouseRepository, public MaterialStockWarehouseRepository $materialStockWarehouseRepository, public UserRepository $userRepository, private readonly EntityManagerInterface $entityManager,)
    {
    }

    #[Route('/api/stock', name: 'add-stock', methods: 'POST')]
    public function addStock(Request $request): JsonResponse
    {
        $request = json_decode($request->getContent(), true);

        $warehouse = $this->warehouseRepository->find($request['warehouseId']);
        $material = $this->materialRepository->find($request['materialId']);
        $user = $this->userRepository->find($request['userId']);

        $stock = $this->materialStockWarehouseRepository->findOneBy([
            'warehouse' => $warehouse,
            'material' => $material,
            'user' => $user,
        ]);

        if ($stock){
            return new JsonResponse([
                'message' => 'You already have this material in this warehouse. Please try to update.',
            ], Response::HTTP_BAD_REQUEST
            );
        }

        if ($warehouse && $material){
            if ($warehouse->getCurrentNumberOfMaterials() === $warehouse->getCapacity()){
                return new JsonResponse([
                    'message' => 'Warehouse capacity is full',
                ], Response::HTTP_BAD_REQUEST
                );
            }elseif ($warehouse->getCapacity() < $warehouse->getCurrentNumberOfMaterials() + $request['quantity']){
                return new JsonResponse([
                    'message' => 'There is no place to take this quantity',
                ], Response::HTTP_BAD_REQUEST
                );
            }

            $stock = $this->materialStockWarehouseRepository->findOneBy([
                'warehouse' => $warehouse,
                'material' => $material,
                'user' => $user,
            ]);

             $addStock = $this->materialStockWarehouseRepository->add(
                $warehouse,
                $material,
                $user,
                $request['quantity']
            );

            return new JsonResponse([
                'message' => 'Stock created successfully'
            ], Response::HTTP_CREATED);
        }else {
            return new JsonResponse([
                'message' => 'Material or Warehouse is not found.',
            ], Response::HTTP_NOT_FOUND);
        }
    }
    #[Route('/api/stock/edit/{warehouseId}/{materialId}/{userId}', name: 'update-stock', methods: 'PATCH')]
    public function edit(int $warehouseId, int $materialId, int $userId, Request $request): JsonResponse
    {
        $request = json_decode($request->getContent(), true);

        $warehouse = $this->warehouseRepository->find($warehouseId);
        $material = $this->materialRepository->find($materialId);
        $user = $this->userRepository->find($userId);

        if ($warehouse && $material && $user){
            $stock = $this->materialStockWarehouseRepository->findOneBy([
                'warehouse' => $warehouse,
                'material' => $material,
                'user' => $user,
            ]);

            if ($warehouse->getCapacity() < $warehouse->getCurrentNumberOfMaterials() + ($request['quantity'] - $stock->getQuantity()) ) {
                return new JsonResponse([
                    'message' => 'There is no place to take this quantity',
                ], Response::HTTP_BAD_REQUEST);
            }

            $warehouse->setCurrentNumberOfMaterials($warehouse->getCurrentNumberOfMaterials() + ($request['quantity'] - $stock->getQuantity()));

            $stock->setQuantity($request['quantity']);

            $this->entityManager->persist($warehouse);
            $this->entityManager->persist($stock);
            $this->entityManager->flush();

        }else {
            return new JsonResponse([
                'message' => 'Material, Warehouse or User is not found.',
            ], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse([
            'message' => 'Stock updated successfully.',
        ], Response::HTTP_CREATED);
    }

    #[Route('/api/stock/delete/{warehouseId}/{materialId}/{userId}', name: 'delete-stock', methods: 'DELETE')]
    public function delete(int $warehouseId, int $materialId, int $userId): JsonResponse
    {
        $warehouse = $this->warehouseRepository->find($warehouseId);
        $material = $this->materialRepository->find($materialId);
        $user = $this->userRepository->find($userId);

        if ($warehouse && $material && $user){
            $stock = $this->materialStockWarehouseRepository->findOneBy([
                'warehouse' => $warehouse,
                'material' => $material,
                'user' => $user,
            ]);

            $warehouse->setCurrentNumberOfMaterials($warehouse->getCurrentNumberOfMaterials() - $stock->getQuantity());

            $this->entityManager->persist($warehouse);
            $this->entityManager->remove($stock);
            $this->entityManager->flush();

        }else {
            return new JsonResponse([
                'message' => 'Material, Warehouse or User is not found.',
            ], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse([
            'message' => 'Material deleted successfully',
        ], Response::HTTP_FOUND);
    }

}
