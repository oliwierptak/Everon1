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

class ForeignKey extends Constraint implements Schema\ForeignKey 
{
    protected $referenced_table_name = null;
    
    protected $referenced_column_name = null;

    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->unlock();

        $PrimaryKeyInfo = new Helper\PopoProps($data);
        $this->referenced_table_name = $PrimaryKeyInfo->foreign_table_name;
        $this->referenced_column_name = $PrimaryKeyInfo->foreign_column_name;
        $this->lock();
    }
    

    public function setReferencedTableName($referenced_table_name)
    {
        $this->referenced_table_name = $referenced_table_name;
    }

    public function getReferencedTableName()
    {
        return $this->referenced_table_name;
    }
    
    public function setReferencedColumnName($referenced_column_name)
    {
        $this->referenced_column_name = $referenced_column_name;
    }

    public function getReferencedColumnName()
    {
        return $this->referenced_column_name;
    }
}
