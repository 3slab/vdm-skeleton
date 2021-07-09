<?php

namespace App\Manager;

use App\Helper\RequestFilterHelper;
use App\Repository\MeteoRepository;
use Doctrine\Common\Collections\Criteria;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class MeteoManager
 * @package App\Manager
 */
class MeteoManager
{
    /**
     * @var MeteoRepository
     */
    protected $repository;

    /**
     * @var PaginatorInterface
     */
    protected $paginator;

    /**
     * MeteoManager constructor.
     * @param MeteoRepository $repository
     * @param PaginatorInterface $paginator
     */
    public function __construct(
        MeteoRepository $repository,
        PaginatorInterface $paginator
    ) {
        $this->repository = $repository;
        $this->paginator = $paginator;
    }

    /**
     * @param Criteria $criteria
     * @param int $page
     * @param int $itemPerPage
     * @return PaginationInterface
     * @throws \Doctrine\ORM\Query\QueryException
     */
    public function list(Criteria $criteria, int $page = 1, int $itemPerPage = 10): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->repository->listQb($criteria),
            $page,
            $itemPerPage
        );
    }

    /**
     * @param Request $request
     * @return Criteria
     * @throws \Exception
     */
    public function createCriteriaFromRequest(Request $request): Criteria
    {
        return RequestFilterHelper::extractFiltersFromRequest(
            $request,
            [
                [
                    'request_key'     => 'startAt',
                    'operator'        => RequestFilterHelper::GT,
                    'entity_property' => 'date',
                ],
                [
                    'request_key'     => 'endAt',
                    'operator'        => RequestFilterHelper::LT,
                    'entity_property' => 'data',
                ]
            ]
        );
    }
}
