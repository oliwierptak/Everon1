<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\DataMapper\Schema;

use Everon\Helper;
use Everon\DataMapper\Exception;
use Everon\DataMapper\Interfaces\Schema;

abstract class Column implements Schema\Column 
{
    use Helper\Immutable;
    
    
    protected $is_pk = null;
    
    protected $is_unique = null;

    protected $name = null;

    protected $type = null;
    
    protected $is_autoincremental = null;
    
    protected $length = null;
    
    protected $precision = null;
    
    protected $is_nullable = null;
    
    protected $default = null;
    
    protected $encoding = null;
    
    protected $ColumnInfo = null;
    
    protected $validation_rules = null;
    
    abstract protected function init(array $data);
    

    public function __construct(array $data)
    {
        $this->init($data);
        $this->lock();
    }
    
    public function isPk()
    {
        return $this->is_pk;
    }
    
    public function isAutoIncremental()
    {
        return $this->is_autoincremental;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function getType()
    {
        return $this->type;
    }
    
    public function getLength()
    {
        return $this->length;
    }
    
    public function isNullable()
    {
        return $this->is_nullable;
    }
    
    public function getDefault()
    {
        return $this->default;
    }

    public function toArray()
    {
        return get_object_vars($this);
    }
    
    public function __toString()
    {
        return (string) $this->name;
    }
}