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
use Everon\DataMapper\Interfaces\Schema;

class Constraint implements Schema\Constraint 
{
    use Helper\Immutable;

    protected $catalog = null;
    protected $schema = null;
    protected $name = null;
    protected $table_schema = null;
    protected $table_name = null;
    protected $type = null;
    
    protected $ConstraintInfo = null;
    
    
    public function __construct(array $data)
    {
        $this->ConstraintInfo = new Helper\PopoProps($data);
       
        $this->catalog = $this->ConstraintInfo->constraint_catalog;
        $this->schema = $this->ConstraintInfo->constraint_schema;
        $this->name = $this->ConstraintInfo->constraint_name;
        $this->table_schema = $this->ConstraintInfo->table_schema;
        $this->table_name = $this->ConstraintInfo->table_name;
        $this->type = $this->ConstraintInfo->constraint_type;
    }
    
    public function toArray()
    {
        return get_object_vars($this);
    }

}
