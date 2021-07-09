<?php

namespace App\Helper;

use DateTime;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\HttpFoundation\Request;

/**
 * class RequestFilterHelper
 * @package App\Helper
 */
class RequestFilterHelper
{
    public const GT   = 'gt';
    public const GTOR = 'gtor';
    public const LT   = 'lt';
    public const LTOR = 'ltor';
    public const EQ   = 'eq';
    public const EQOR = 'eqor';
    public const LIKE = 'like';
    public const IN   = 'in';

    /**
     * @param Request $request
     * @param array $mapping
     * @return Criteria
     *
     * @throws \Exception
     */
    public static function extractFiltersFromRequest(Request $request, array $mapping): Criteria
    {
        $criteria = new Criteria();

        foreach ($mapping as $filter) {
            $filterValue = $request->query->get($filter['request_key'], false);

            if (
                $filterValue && !is_array($filter['entity_property']) &&
                preg_match('/date/i', $filter['entity_property'])
            ) {
                $filterValue = new DateTime($filterValue);
            }

            if ($filterValue) {
                switch ($filter['operator']) {
                    case static::EQ:
                        $criteria->andWhere($criteria->expr()->eq($filter['entity_property'], $filterValue));
                        break;
                    case static::EQOR:
                        $expr = [];
                        foreach ($filter['entity_property'] as $property) {
                            $expr[] = $criteria->expr()->eq($filter['left_join_table'] . "." . $property, $filterValue);
                        }
                        $criteria->andWhere(call_user_func_array(array($criteria->expr(), 'orX'), $expr));
                        break;
                    case static::GT:
                        $criteria->andWhere($criteria->expr()->gt($filter['entity_property'], $filterValue));
                        break;
                    case static::GTOR:
                        $expr = [];
                        $expr[] = $criteria->expr()->gte($filter['entity_property'], $filterValue);

                        foreach ($filter['left_join_table'] as $table) {
                            $expr[] = $criteria->expr()->gte($table . "." . $filter['entity_property'], $filterValue);
                        }
                        $criteria->andWhere(call_user_func_array(array($criteria->expr(), 'orX'), $expr));
                        break;
                    case static::LT:
                        $criteria->andWhere($criteria->expr()->lt($filter['entity_property'], $filterValue));
                        break;
                    case static::LTOR:
                        $expr = [];
                        $expr[] = $criteria->expr()->lt($filter['entity_property'], $filterValue);
                        foreach ($filter['left_join_table'] as $table) {
                            $expr[] = $criteria->expr()->lt($table . "." . $filter['entity_property'], $filterValue);
                        }
                        $criteria->andWhere(call_user_func_array(array($criteria->expr(), 'orX'), $expr));
                        break;
                    case static::LIKE:
                        $criteria->andWhere(
                            $criteria->expr()->startsWith($filter['entity_property'], '%' . $filterValue)
                        );
                        break;
                    case static::IN:
                        $filterValue = explode(',', $filterValue);
                        $criteria->andWhere($criteria->expr()->in($filter['entity_property'], $filterValue));
                        break;
                    default:
                        throw new \InvalidArgumentException("unknown operator: " . $filter['operator']);
                }
            }
        }

        return $criteria;
    }
}
