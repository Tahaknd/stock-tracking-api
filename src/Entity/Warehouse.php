<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\WarehouseRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WarehouseRepository::class)]
class Warehouse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[ApiProperty(description: 'Warehouse Name')]
    private ?string $name = null;

    #[ORM\Column]
    #[ApiProperty(description: 'Maximum capacity of the warehouse')]
    private ?int $capacity = null;

    #[ORM\Column]
    private ?int $currentNumberOfMaterials = 0;

    #[ORM\OneToMany(mappedBy: 'warehouse', targetEntity: MaterialStockWarehouse::class)]
    #[ApiProperty(readable: false, writable: false, default: null)]
    private Collection $warehouseStockMaterials;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCapacity(): int
    {
        return $this->capacity;
    }

    public function setCapacity(int $capacity): static
    {
        $this->capacity = $capacity;

        return $this;
    }

    public function addMaterialStockWarehouse(MaterialStockWarehouse $materialStockWarehouse): static
    {
        if (!$this->warehouseStockMaterials->contains($materialStockWarehouse)) {
            $this->warehouseStockMaterials->add($materialStockWarehouse);
            $materialStockWarehouse->setWarehouse($this);
        }

        return $this;
    }

    public function removeMaterialStockWarehouse(MaterialStockWarehouse $materialStockWarehouse): static
    {
        if ($this->warehouseStockMaterials->removeElement($materialStockWarehouse)) {
            if ($materialStockWarehouse->getWarehouse() === $this) {
                $materialStockWarehouse->setWarehouse(null);
            }
        }

        return $this;
    }

    #[ApiProperty(readable: false, writable: false, default: null)]
    public function getMaterials()
    {
        return array_map(function ($warehouseMaterial) {
            return [
                'id' => $warehouseMaterial->getProduct()->getId(),
                'name' => $warehouseMaterial->getProduct()->getName(),
                'quantity' => $warehouseMaterial->getQuantity(),
            ];
        }, $this->getWarehouseStockMaterials()->toArray());
    }

    /**
     * @return Collection
     */
    public function getWarehouseStockMaterials(): Collection
    {
        return $this->warehouseStockMaterials;
    }
}
