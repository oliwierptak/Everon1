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

use Everon\DataMapper\Interfaces\Schema;

use Everon\Helper;

class PrimaryKey extends Constraint implements Schema\PrimaryKey
{
    protected $name = null;

    protected $table_name = null;
    
    
    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->unlock();
        
        $PrimaryKeyInfo = new Helper\PopoProps($data);
        $this->name = $PrimaryKeyInfo->column_name;
        $this->lock();
    }
}
