<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Controller\MaterialStockWarehouseController;
use App\Repository\MaterialStockWarehouseRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MaterialStockWarehouseRepository::class)]
#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/stock',
            controller: MaterialStockWarehouseController::class,
            description: 'Stock Entry',
            name: 'add-stock',
        ),
        new Patch(
            uriTemplate: '/stock/edit/{warehouseId}/{materialId}/{userId}',
            uriVariables: ['warehouseId', 'materialId', 'userId'],
            controller: MaterialStockWarehouseController::class,
            description: 'Update Stock',
            name: 'update-stock',
        ),
        new Delete(
            uriTemplate: '/stock/delete/{warehouseId}/{materialId}/{userId}',
            uriVariables: ['warehouseId', 'materialId', 'userId'],
            controller: MaterialStockWarehouseController::class,
            description: 'Remove Stock',
            name: 'delete-stock'
        ),
    ],
    formats: ['json' => ['application/json']]
)]
class MaterialStockWarehouse
{
    #[ORM\ManyToOne(inversedBy: 'warehouseStockMaterials')]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\Id]
    #[ApiProperty(description: 'Material Name', readable: false, writable: false, schema: ['type' => 'string', 'example' => 'screwdriver'])]
    private ?Material $material = null;
    #[ORM\ManyToOne(inversedBy: 'warehouseStockMaterials')]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\Id]
    #[ApiProperty(description: 'Warehouse Name', readable: false, writable: false, schema: ['type' => 'string', 'example' => 'ExampleWarehouse'])]
    private ?Warehouse $warehouse = null;

    #[ORM\ManyToOne(inversedBy: 'warehouseStockMaterials')]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\Id]
    #[ApiProperty(description: 'User Name', readable: false, writable: false, schema: ['type' => 'string', 'example' => 'ExampleUser'])]
    private ?User $user = null;

    #[ORM\Column]
    #[ApiProperty(description: 'Stock Quantity', schema: ['type' => 'integer', 'examples' => [1]])]
    private ?int $quantity = null;

    #[ApiProperty(description: 'Material ID', schema: ['type' => 'integer', 'examples' => [1]])]
    private ?int $materialId = null;
    #[ApiProperty(description: 'Warehouse ID', schema: ['type' => 'integer', 'examples' => [1]])]
    private ?int $warehouseId = null;

    #[ApiProperty(description: 'User ID', schema: ['type' => 'integer', 'examples' => [1]])]
    private ?int $userId = null;

    public function getMaterial(): ?Material
    {
        return $this->material;
    }

    public function setMaterial(?Material $material): static
    {
        $this->material = $material;

        return $this;
    }

    public function getWarehouse(): ?Warehouse
    {
        return $this->warehouse;
    }

    public function setWarehouse(?Warehouse $warehouse): static
    {
        $this->warehouse = $warehouse;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getUserId(): ?int
    {
        return $this->userId;
    }

    /**
     * @param int|null $userId
     */
    public function setUserId(?int $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * @return int|null
     */
    public function getWarehouseId(): ?int
    {
        return $this->warehouseId;
    }

    /**
     * @param int|null $warehouseId
     */
    public function setWarehouseId(?int $warehouseId): void
    {
        $this->warehouseId = $warehouseId;
    }

    /**
     * @return int|null
     */
    public function getMaterialId(): ?int
    {
        return $this->materialId;
    }

    /**
     * @param int|null $materialId
     */
    public function setMaterialId(?int $materialId): void
    {
        $this->materialId = $materialId;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User|null $user
     */
    public function setUser(?User $user): void
    {
        $this->user = $user;
    }
}
