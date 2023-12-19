<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Tests\Fixtures\Metadata\Get;
use App\Controller\WarehouseController;
use App\Repository\WarehouseRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WarehouseRepository::class)]
#[ApiResource(
    description: 'Create Warehouse',
    operations: [
        new Get(
            uriTemplate: '/warehouse/{id}',
            uriVariables: 'id',
            controller: WarehouseController::class,
            name: 'show_warehouse',
        ),
        new Post(),
        new Patch(),
        new Delete(),
    ],
    formats: ['json' => ['application/json']]
)]
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCapacity(): int
    {
        return $this->capacity;
    }

    /**
     * @return int|null
     */
    public function getCurrentNumberOfMaterials(): ?int
    {
        return $this->currentNumberOfMaterials;
    }

    public function setCurrentNumberOfMaterials(int $currentNumberOfMaterials): static
    {
        $this->currentNumberOfMaterials = $currentNumberOfMaterials;

        return $this;
    }

    public function setCapacity(int $capacity): static
    {
        $this->capacity = $capacity;

        return $this;
    }

    public function addMaterialStockWarehouse(MaterialStockWarehouse $warehouseStockMaterial): static
    {
        if (!$this->warehouseStockMaterials->contains($warehouseStockMaterial)) {
            $this->warehouseStockMaterials->add($warehouseStockMaterial);
            $warehouseStockMaterial->setWarehouse($this);
        }

        return $this;
    }

    public function removeMaterialStockWarehouse(MaterialStockWarehouse $warehouseStockMaterial): static
    {
        if ($this->warehouseStockMaterials->removeElement($warehouseStockMaterial)) {
            if ($warehouseStockMaterial->getWarehouse() === $this) {
                $warehouseStockMaterial->setWarehouse(null);
            }
        }

        return $this;
    }

    #[ApiProperty(readable: false, writable: false, default: null)]
    public function getMaterials()
    {
        return array_map(function ($warehouseMaterial) {
            return [
                'id' => $warehouseMaterial->getMaterial()->getId(),
                'name' => $warehouseMaterial->getMaterial()->getName(),
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
