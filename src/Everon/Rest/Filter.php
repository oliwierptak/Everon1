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

use Everon\Dependency;
use Everon\Helper;
use Everon\Rest\Exception;


class Filter implements Interfaces\Filter
{
    use Dependency\Injection\Factory;
    
    public function toCriteria(array $filter)
    {
        
        $expressions = [];
        foreach ($filter as $key => $data) {
            $key = strtoupper($key);
            
            switch ($key) {
                case 'WHERE':
                    $CriteriaBuilder = $this->getFactory()->buildCriteriaBuilder();
                    $CriteriaB$expressionsuilder->where($column, $operator, $value);
                    $this->parseSubFilter($CriteriaBuilder, $data);
                    break;
                case 'AND':
                    $CriteriaBuilder = $this->getFactory()->buildCriteriaBuilder();
                    $CriteriaBuilder->andWhere($column, $operator, $value);
                    $this->parseSubFilter($CriteriaBuilder, $data);
                    $CriteriaBuilder->glueByAnd();
                    break;
                case 'OR':
                    $CriteriaBuilder = $this->getFactory()->buildCriteriaBuilder();
                    $CriteriaBuilder->orWhere($column, $operator, $value);
                    $this->parseSubFilter($CriteriaBuilder, $data);
                    $CriteriaBuilder->glueByOr();
                    break;
            }

            $expressions[] = $CriteriaBuilder;
        }
        
        return $CriteriaBuilder;
    }
    
    protected function parseSubFilter($CriteriaBuilder, $data)
    {
        foreach ($data as $sub_data) {
            @list($column, $operator, $value, $glue) = $sub_data;
            $glue = isset($glue) === false ? 'AND' : strtoupper($glue);

            if ($glue === 'AND') {
                $CriteriaBuilder->andWhere($column, $operator, $value);
            }
            if ($glue === 'OR') {
                $CriteriaBuilder->orWhere($column, $operator, $value);
            }
        }
    }
}