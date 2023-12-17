<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use App\Repository\MaterialStockWarehouseRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MaterialStockWarehouseRepository::class)]
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

    #[ORM\Column]
    #[ApiProperty(description: 'Stock Quantity', schema: ['type' => 'integer', 'examples' => [1]])]
    private ?int $quantity = null;

    #[ApiProperty(description: 'Material ID', schema: ['type' => 'integer', 'examples' => [1]])]
    private ?int $materialId = null;
    #[ApiProperty(description: 'Warehouse ID', schema: ['type' => 'integer', 'examples' => [1]])]
    private ?int $warehouseId = null;

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

    public function getMaterialId(): ?int
    {
        return $this->materialId;
    }

    public function getWarehouseId(): ?int
    {
        return $this->warehouseId;
    }
}
