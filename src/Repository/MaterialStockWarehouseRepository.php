<?php

namespace App\Repository;

use App\Entity\Material;
use App\Entity\MaterialStockWarehouse;
use App\Entity\User;
use App\Entity\Warehouse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use http\Env\Response;

/**
 * @extends ServiceEntityRepository<MaterialStockWarehouse>
 *
 * @method MaterialStockWarehouse|null find($id, $lockMode = null, $lockVersion = null)
 * @method MaterialStockWarehouse|null findOneBy(array $criteria, array $orderBy = null)
 * @method MaterialStockWarehouse[]    findAll()
 * @method MaterialStockWarehouse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MaterialStockWarehouseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MaterialStockWarehouse::class);
    }

//    /**
//     * @return MaterialStockWarehouse[] Returns an array of MaterialStockWarehouse objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?MaterialStockWarehouse
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function add(Warehouse $warehouse, Material $material, User $user, $quantity): MaterialStockWarehouse
    {

        $warehouseStockMaterial = new MaterialStockWarehouse();
        $warehouseStockMaterial->setWarehouse($warehouse);
        $warehouseStockMaterial->setMaterial($material);
        $warehouseStockMaterial->setUser($user);
        $warehouseStockMaterial->setQuantity($quantity);

        $currentCapacity = $warehouse->getCurrentNumberOfMaterials();
        $warehouse->setCurrentNumberOfMaterials($currentCapacity + $quantity);

        $entityManager = $this->getEntityManager();
        $entityManager->persist($warehouse);
        $entityManager->persist($warehouseStockMaterial);
        $entityManager->flush();

        return $warehouseStockMaterial;
    }
}
