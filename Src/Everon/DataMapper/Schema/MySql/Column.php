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
    use Helper\Arrays;
    
    protected function init(array $data, array $primary_key_list, array $unique_key_list, array $foreign_key_list)
    {
        $ColumnInfo = new Helper\PopoProps($data);
        $this->is_pk = ($ColumnInfo->column_key ==  'PRI');
        $this->is_unique = ($ColumnInfo->column_key ==  'UNI');
        $this->name = $ColumnInfo->column_name;
        $this->is_autoincremental = strpos($ColumnInfo->extra, 'auto_increment') === true;
        $this->is_nullable = ($ColumnInfo->is_nullable == 'YES');
        $this->default = $ColumnInfo->column_default;
        $this->schema = $ColumnInfo->table_schema;
        
        switch ($type = $ColumnInfo->data_type) {
            case 'text':
            case 'varchar':
                $this->length = (int) $ColumnInfo->character_maximum_length;
                $this->encoding = $ColumnInfo->character_set_name;
                $this->validation_rules = [$this->name => \FILTER_SANITIZE_STRING];
                $this->type = static::TYPE_STRING;
                break;

            case 'tinyint':
            case 'smallint':
            case 'int':
            case 'bigint':
                $this->length = (int) $ColumnInfo->numeric_precision;
                $this->precision = (int) $ColumnInfo->numeric_scale;
                $this->validation_rules = [$this->name => \FILTER_VALIDATE_INT];
                $this->type = static::TYPE_INTEGER;
                break;

            case 'decimal':
                $this->length = (int) $ColumnInfo->numeric_precision;
                $this->precision = (int) $ColumnInfo->numeric_scale;
                $this->validation_rules = [$this->name => \FILTER_VALIDATE_FLOAT];
                $this->type = static::TYPE_FLOAT;
                break;

            case 'timestamp':
                $this->length = 19;
                $this->validation_rules = [$this->name => \FILTER_SANITIZE_STRING];
                $this->type = static::TYPE_TIMESTAMP;
                break;

            default:
                throw new Exception\Column('Unsupported data type: "%s"', $type);
                break;
        }
    }
}
