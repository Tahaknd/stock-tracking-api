<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\MaterialRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MaterialRepository::class)]
#[ApiResource(description: 'Material', formats: ['json' => ['application/json']])]
class Material
{
    //Product nerede oluşuyor ?
    //Controllerda sadece serviceten gelen funclar mı çalışıyor yoksa funclarda data mapping alıp controller methodunda logicleri mi yazıyoruz?

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[ApiProperty(description: 'Material Name')]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'material', targetEntity: MaterialStockWarehouse::class)]
    #[ApiProperty(readable: false, writable: false, default: null)]
    private Collection $warehouseStockMaterials;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function addMaterialStockWarehouse(MaterialStockWarehouse $materialStockWarehouse): static
    {
        if (!$this->warehouseStockMaterials->contains($materialStockWarehouse)) {
            $this->warehouseStockMaterials->add($materialStockWarehouse);
            $materialStockWarehouse->setMaterial($this);
        }

        return $this;
    }

    public function removeMaterialStockWarehouse(MaterialStockWarehouse $materialStockWarehouse): static
    {
        if ($this->warehouseStockMaterials->removeElement($materialStockWarehouse)) {
            if ($materialStockWarehouse->getMaterial() === $this) {
                $materialStockWarehouse->setMaterial(null);
            }
        }

        return $this;
    }
}
