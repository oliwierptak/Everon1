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

    const PARAM_SEPARATOR = ',';

    
    protected $expand = null;
    protected $fields = null;
    protected $order_by = null;
    protected $sort = null;
    

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
        $value = $this->getRequest()->getQueryParameter($name, null);
        if ($value !== null) {
            $value = explode(static::PARAM_SEPARATOR, trim($value, static::PARAM_SEPARATOR)); //eg. date_added,user_name
            if (is_array($value)) {
                return $value;
            }
        }
        
        return $default;
    }
    
    protected function init()
    {
        $this->fields = $this->getParameterValue('fields', []);
        $this->expand = $this->getParameterValue('expand', []);
        $this->order_by = $this->getParameterValue('order_by', []);
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
}