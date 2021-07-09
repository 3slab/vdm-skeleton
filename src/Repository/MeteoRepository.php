<?php

namespace App\Repository;

use App\Entity\Meteo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class MeteoRepository
 * @package App\Repository
 */
class MeteoRepository extends ServiceEntityRepository
{
    /**
     * MeteoRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Meteo::class);
    }

    /**
     * @param Criteria $criteria
     * @return Query
     * @throws Query\QueryException
     */
    public function listQb(Criteria $criteria): Query
    {
        $qb = $this->createQueryBuilder('m');

        $qb
            ->addCriteria($criteria)
            ->addOrderBy('m.date', 'asc');

        return $qb->getQuery();
    }
}
