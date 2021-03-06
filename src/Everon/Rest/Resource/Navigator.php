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

use Everon\Helper;
use Everon\Rest\Dependency;
use Everon\Rest\Exception;
use Everon\Rest\Interfaces;

class Navigator implements Interfaces\ResourceNavigator
{
    use \Everon\Dependency\Injection\Factory;
    use Dependency\Request;

    use Helper\Arrays;
    use Helper\IsIterable;

    const PARAM_SEPARATOR = ',';


    /**
     * @var array
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
     * @var bool
     */
    protected $initialized = false;
    

    /**
     * @param Interfaces\Request $Request
     */
    public function __construct(Interfaces\Request $Request)
    {
        $this->Request = $Request;
    }

    /**
     * @param $name
     * @param null $default_value
     * @return array|mixed|null
     */
    protected function getParameterValue($name, $default_value=null)
    {
        $as_array = is_array($default_value);
        $parameter_value = $this->getRequest()->getGetParameter($name, null);
        if ($parameter_value !== null) {
            if ($as_array || strpos($parameter_value, static::PARAM_SEPARATOR) !== false) {
                $parameter_value = explode(static::PARAM_SEPARATOR, trim($parameter_value, static::PARAM_SEPARATOR)); //eg. date_added,user_name
                if (is_array($parameter_value)) {
                    return $parameter_value;
                }
            }
            else {
                return $this->getRequest()->getQueryParameter($name, $default_value);
            }
        }
        
        return $default_value;
    }
    
    protected function init()
    {
        if ($this->initialized) {
            return;
        }
        
        $this->fields = $this->getParameterValue('fields', []);
        $this->expand = $this->getParameterValue('expand', []);
        $this->limit = $this->getRequest()->getGetParameter('limit', 10);
        $this->offset = $this->getRequest()->getGetParameter('offset', 0);
        $this->filters = $this->getRequest()->getGetParameter('filters');
        $this->filters = json_decode($this->filters, true);
        
        $order_by_data = $this->getParameterValue('order_by', []);

        $collection = $this->getRequest()->getQueryParameter('collection', null);
        if ($collection !== null) {
            $this->expand = array_merge($this->expand, [$collection]);
        }

        for ($a=0; $a<count($order_by_data); $a++) { //eg. -date_added, user_name //show recently added users
            $name = $order_by_data[$a];
            $field_name = ltrim($name, '-');
            $this->order_by[$field_name] = 'ASC';
            if ($name[0] === '-') {
                $this->order_by[$field_name] = 'DESC';
            }
        }
        
        $this->initialized = true;
    }

    /**
     * @inheritdoc
     */
    public function setExpand(array $expand)
    {
        $this->expand = $expand;
    }

    /**
     * @inheritdoc
     */
    public function getExpand()
    {
        $this->init();
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
        $this->init();
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
        $this->init();
        return $this->order_by;
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
        $this->init();
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
        $this->init();
        return $this->limit;
    }

    /**
     * @param array $filters
     */
    public function setFilters(array $filters)
    {
        $this->filters = $filters;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        $this->init();
        return $this->filters;
    }

    /**
     * @inheritdoc
     */
    public function toCriteria($resource_name)
    {
        $this->init();
        
        $column = null;
        $CriteriaBuilder = $this->getFactory()->buildCriteriaBuilder();
        $filters = $this->getFilters();

        if (empty($filters) === false) {
            $organized_filters = [];
            
            foreach ($filters as $key => $values) {
                $collection_name = null;
                
                foreach ($values as $v => $vv) {
                    if (is_string($vv) && strpos($vv, '.') !== false) {
                        $tokens = explode('.', $vv); //eg. foo.bar.zzz
                        $collection_name = array_shift($tokens);
                        $column = current($tokens);
                        $organized_filters[$collection_name][$key] = $filters[$key];
                        $organized_filters[$collection_name][$key][$v] = $column;
                    }
                }
            }

            if (isset($organized_filters[$resource_name]) !== false) {
                $Filter = $this->getFactory()->buildRestFilter();
                $CriteriaBuilder = $Filter->toCriteria($organized_filters[$resource_name]);
            }
        }
        
        $CriteriaBuilder->setLimit($this->getLimit())
            ->setOffset($this->getOffset())
            ->setOrderBy($this->getOrderBy() ?: []);
        
        return $CriteriaBuilder;
    }
}