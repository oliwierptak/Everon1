<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Rest;

use Everon\DataMapper;
use Everon\Dependency;


class Filter implements Interfaces\Filter
{
    use Dependency\Injection\Factory;

    /**
     * @param array $filter
     * @return DataMapper\Interfaces\Criteria\Builder|void
     * @throws Exception\Filter
     */
    public function toCriteria(array $filter)
    {
        try {
            $CriteriaBuilder = $this->getFactory()->buildCriteriaBuilder();

            $where_item = array_shift($filter);
            @list($column, $operator, $value) = $where_item;
            $CriteriaBuilder->where($column, $operator, $value);

            foreach ($filter as $item) {
                @list($column, $operator, $value, $glue) = $item;
                $glue = isset($glue) === false ? 'AND' : strtoupper($glue);

                if ($glue === DataMapper\Criteria\Builder::GLUE_AND) {
                    $CriteriaBuilder->andWhere($column, $operator, $value);
                }

                if ($glue === DataMapper\Criteria\Builder::GLUE_OR) {
                    $CriteriaBuilder->orWhere($column, $operator, $value);
                }
            }

            return $CriteriaBuilder;            
        }
        catch (\Exception $e) {
            throw new Exception\Filter($e);
        }
    }
}