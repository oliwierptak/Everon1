<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\DataMapper\Schema\MySql;

use Everon\Helper;
use Everon\DataMapper\Exception;
use Everon\DataMapper\Schema;

class Column extends Schema\Column
{
    protected function init(array $data)
    {
        $this->ColumnInfo = new Helper\PopoProps($data);
        $this->is_pk = ($this->ColumnInfo->column_key ==  'PRI');
        $this->is_unique = ($this->ColumnInfo->column_key ==  'UNI');
        $this->name = $this->ColumnInfo->column_name;
        $this->is_autoincremental = strpos($this->ColumnInfo->extra, 'auto_increment') === true;
        $this->is_nullable = ($this->ColumnInfo->is_nullable == 'YES');
        $this->default = $this->ColumnInfo->column_default;
        
        switch ($type = $this->ColumnInfo->data_type) {
            case 'text':
            case 'varchar':
                $this->length = (int) $this->ColumnInfo->character_maximum_length;
                $this->encoding = $this->ColumnInfo->character_set_name;
                $this->validation_rules = array(\FILTER_SANITIZE_STRING);
                $this->type = 'string';
                break;

            case 'tinyint':
            case 'smallint':
            case 'int':
            case 'bigint':
                $this->length = (int) $this->ColumnInfo->numeric_precision;
                $this->precision = (int) $this->ColumnInfo->numeric_scale;
                $this->validation_rules = array(\FILTER_VALIDATE_INT);
                $this->type = 'integer';
                break;

            case 'decimal':
                $this->length = (int) $this->ColumnInfo->numeric_precision;
                $this->precision = (int) $this->ColumnInfo->numeric_scale;
                $this->validation_rules = array(\FILTER_VALIDATE_FLOAT);
                $this->type = 'float';
                break;

            case 'timestamp':
                $this->length = 19;
                $this->validation_rules = array(\FILTER_SANITIZE_STRING);
                $this->type = 'timestamp';
                break;

            default:
                throw new Exception\Column('Unsupported data type: %s', $type);
                break;
        }
    }
}
