<?php

namespace App\Service;

use App\Entity\MaterialStockWarehouse;
use App\Repository\MaterialRepository;
use App\Repository\MaterialStockWarehouseRepository;
use App\Repository\UserRepository;
use App\Repository\WarehouseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StockService
{
    public function __construct(
        public MaterialRepository                $materialRepository,
        public WarehouseRepository              $warehouseRepository,
        public MaterialStockWarehouseRepository $materialStockWarehouseRepository,
        public UserRepository                   $userRepository,
        public readonly EntityManagerInterface  $entityManager
    )
    {

    }

    public function getMaterialStock(int $warehouseId, int $materialId, int $userId): array
    {
        $warehouse = $this->warehouseRepository->find($warehouseId);
        $material = $this->materialRepository->find($materialId);
        $user = $this->userRepository->find($userId);

        if (!$warehouse || !$material || !$user) {
            $errorMessage = !$warehouse ? 'Warehouse' : (!$material ? 'Material' : 'User') . ' is not found.';
            return [
                'status' => Response::HTTP_NOT_FOUND,
                'message' => $errorMessage,
                'data' => null,
            ];
        }

        $materialStock = $this->materialStockWarehouseRepository->findOneBy([
            'warehouse' => $warehouse,
            'material' => $material,
            'user' => $user,
        ]);

        if (null === $materialStock) {
            return [
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'The material is not available in the warehouse',
                'data' => null,
            ];
        }

        return [
            'status' => Response::HTTP_FOUND,
            'message' => 'Material is available in the warehouse',
            'data' => $materialStock,
        ];
    }

    public function createNewStock($request): JsonResponse
    {
        $warehouse = $this->warehouseRepository->find($request['warehouseId']);
        $material = $this->materialRepository->find($request['materialId']);
        $user = $this->userRepository->find($request['userId']);

        $materialStockExists = $this->materialStockWarehouseRepository->findOneBy([
            'warehouse' => $warehouse,
            'material' => $material,
            'user' => $user,
        ]);

        $checkCapacityRule = $this->checkCapacityRule($warehouse, $request['quantity']);

        if ($warehouse->getCurrentNumberOfMaterials() === $warehouse->getCapacity()) {
            return new JsonResponse([
                'message' => 'Warehouse capacity is full',
            ], Response::HTTP_BAD_REQUEST);
        } elseif ($checkCapacityRule) {
            return new JsonResponse([
                'message' => 'There is no place to take this quantity.Maximum capacity it can take: '.$this->getCheckEmptyCapacityOfWarehouse($warehouse),
            ], Response::HTTP_BAD_REQUEST);
        }

        if ($materialStockExists){
            return new JsonResponse([
                'message' => 'You already have this material in this warehouse. Please try to update.',
            ], Response::HTTP_BAD_REQUEST
            );
        }

        $addStock = $this->materialStockWarehouseRepository->add(
            $warehouse,
            $material,
            $user,
            $request['quantity']
        );

        return new JsonResponse([
            'message' => 'Stock created successfully',
        ], Response::HTTP_CREATED);
    }

    public function updateCurrentStock(MaterialStockWarehouse $materialInStock, ?int $quantity): JsonResponse|array
    {
        $warehouse = $materialInStock->getWarehouse();
        $checkCapacity = $this->checkCapacityRuleForUpdate($warehouse, $quantity, $materialInStock);

        if ($checkCapacity){
            return [
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => 'There is no place to take this quantity. Your current quantity: ' . $materialInStock->getQuantity() . 'Maximum capacity it can take: '. $this->getCheckEmptyCapacityOfWarehouse($warehouse),
            ];
        }

        $warehouse->setCurrentNumberOfMaterials($warehouse->getCurrentNumberOfMaterials() + ($quantity - $materialInStock->getQuantity()));

        $materialInStock->setQuantity($quantity);

        $this->entityManager->persist($materialInStock);
        $this->entityManager->flush();

        return [
            'storage' => [
                'id' => $materialInStock->getWarehouse()->getId(),
                'name' => $materialInStock->getWarehouse()->getName(),
            ],
            'product' => [
                'id' => $materialInStock->getMaterial()->getId(),
                'name' => $materialInStock->getMaterial()->getName(),
            ],
            'quantity' => $materialInStock->getQuantity(),
            'status' => Response::HTTP_OK,
        ];
    }

    public function deleteStock(MaterialStockWarehouse $materialInStock)
    {
        $warehouse = $materialInStock->getWarehouse();

        $warehouse->setCurrentNumberOfMaterials($warehouse->getCurrentNumberOfMaterials() - $materialInStock->getQuantity());

        $this->entityManager->persist($warehouse);
        $this->entityManager->remove($materialInStock);
        $this->entityManager->flush();
    }

    public function getCheckEmptyCapacityOfWarehouse($warehouse): int
    {
        return $warehouse->getCapacity() - $warehouse->getCurrentNumberOfMaterials();
    }

    public function checkCapacityRule($warehouse, $quantity): bool
    {
       $capacityAvailability = $warehouse->getCapacity() < $warehouse->getCurrentNumberOfMaterials() + $quantity;

       if ($capacityAvailability){
           return true;
       }

       return false;
    }

    public function checkCapacityRuleForUpdate($warehouse, $quantity,MaterialStockWarehouse $materialInStock): bool
    {
        $currentQuantity = $materialInStock->getQuantity();
        $capacityAvailability = $warehouse->getCapacity() < $warehouse->getCurrentNumberOfMaterials() + ($quantity - $currentQuantity);


        if ($capacityAvailability){
            return true;
        }

        return false;
    }


}