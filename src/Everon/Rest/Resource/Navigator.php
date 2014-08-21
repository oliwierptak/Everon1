<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Rest\Resource;

use Everon\Rest\Dependency;
use Everon\Helper;
use Everon\Rest\Interfaces;

class Navigator implements Interfaces\ResourceNavigator
{
    use Dependency\Request;
    use \Everon\Dependency\Injection\Factory;

    const PARAM_SEPARATOR = ',';


    /**
     * @var string
     */
    protected $expand = null;

    /**
     * @var string
     */
    protected $fields = null;

    /**
     * @var string
     */
    protected $order_by = null;

    /**
     * @var string
     */
    protected $sort = null;

    /**
     * @var string
     */
    protected $offset = null;

    /**
     * @var string
     */
    protected $limit = null;

    /**
     * @var string
     */
    protected $filters = null;
    

    /**
     * @param Interfaces\Request $Request
     */
    public function __construct(Interfaces\Request $Request)
    {
        $this->Request = $Request;
        $this->init();
    }

    /**
     * @param $name
     * @param null $default
     * @return array|mixed|null
     */
    protected function getParameterValue($name, $default=null)
    {
        $as_array = is_array($default);
        $value = $this->getRequest()->getGetParameter($name, null);
        if ($value !== null) {
            if ($as_array || strpos($value, static::PARAM_SEPARATOR) !== false) {
                $value = explode(static::PARAM_SEPARATOR, trim($value, static::PARAM_SEPARATOR)); //eg. date_added,user_name
                if (is_array($value)) {
                    return $value;
                }
            }
            else {
                return $this->getRequest()->getQueryParameter($name, $default);
            }
        }
        
        return $default;
    }
    
    protected function init()
    {
        $this->fields = $this->getParameterValue('fields', []);
        $this->expand = $this->getParameterValue('expand', []);
        $this->order_by = $this->getParameterValue('order_by', []);
        $this->limit = $this->getRequest()->getGetParameter('limit', 10);
        $this->offset = $this->getRequest()->getGetParameter('offset', 0);

        $this->filters = $this->getRequest()->getGetParameter('filters');
        $this->filters = json_decode($this->filters,true);

        $this->sort = [];

        $collection = $this->getRequest()->getQueryParameter('collection', null);
        if ($collection !== null) {
            $this->expand = array_merge($this->expand, [$collection]);
        }

        for ($a=0; $a<count($this->order_by); $a++) { //eg. -date_added, user_name //show recently added users
            $name = $this->order_by[$a];
            $field_name = ltrim($name, '-');
            $this->sort[$field_name] = 'ASC';
            if ($name[0] === '-') {
                $this->sort[$field_name] = 'DESC';
            }
            
            $this->order_by[$a] = $field_name;
        }
    }

    /**
     * @inheritdoc
     */
    public function setExpand($expand)
    {
        $this->expand = $expand;
    }

    /**
     * @inheritdoc
     */
    public function getExpand()
    {
        return $this->expand;
    }

    /**
     * @inheritdoc
     */
    public function setFields(array $fields)
    {
        $this->fields = $fields;
    }

    /**
     * @inheritdoc
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @inheritdoc
     */
    public function setOrderBy(array $order_by)
    {
        $this->order_by = $order_by;
    }

    /**
     * @inheritdoc
     */
    public function getOrderBy()
    {
        return $this->order_by;
    }

    /**
     * @inheritdoc
     */
    public function setSort($sort)
    {
        $this->sort = $sort;
    }

    /**
     * @inheritdoc
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @inheritdoc
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
    }

    /**
     * @inheritdoc
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @inheritdoc
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    /**
     * @inheritdoc
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @inheritdoc
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @inheritdoc
     */
    public function setFilters(array $filters)
    {
        $this->filters = $filters;
    }

    /**
     * @return \Everon\DataMapper\Interfaces\Criteria
     */
    public function toCriteria()
    {
        $Criteria = new \Everon\DataMapper\Criteria();
        $Criteria->limit($this->getLimit());
        $Criteria->offset($this->getOffset());
        $Criteria->orderBy($this->getOrderBy());
        $Criteria->sort($this->getSort());
        
        if (is_array($this->getFilters())) {
            $Filter = $this->getFactory()->buildRestFilter(new Helper\Collection($this->getFilters()));
            $Filter->assignToCriteria($Criteria);
        }

        return $Criteria;
    }
}