<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\DataMapper\Schema\PostgreSql;

use Everon\Helper;
use Everon\DataMapper\Exception;
use Everon\DataMapper\Schema;

class Column extends Schema\Column
{
    use Helper\Arrays;
    

    protected function init(array $data, array $primary_key_list, array $unique_key_list, array $foreign_key_list)
    {
        $ColumnInfo = new Helper\PopoProps($data);
        $this->name = $ColumnInfo->column_name;
        $this->is_pk = $this->hasInfo($this->name, $primary_key_list);
        $this->is_unique = $this->hasInfo($this->name, $unique_key_list) || $this->is_pk;
        $this->is_nullable = ($ColumnInfo->is_nullable == 'YES');
        $this->default = $ColumnInfo->column_default;
        $this->schema = $ColumnInfo->table_schema;
        $this->table = $ColumnInfo->table_name_without_schema;
        
        switch ($type = $ColumnInfo->data_type) {
            case 'char':
            case 'character varying':
            case 'text':
                $this->length = (int) $ColumnInfo->character_maximum_length;
                $this->encoding = $ColumnInfo->character_set_name;
                $this->validation_rules = [$this->name => \FILTER_SANITIZE_STRING];
                $this->type = static::TYPE_STRING;
                break;

            case 'bigint':
            case 'integer':
            case 'smallint':
                $this->length = (int) $ColumnInfo->numeric_precision;
                $this->precision = (int) $ColumnInfo->numeric_scale;
                $this->validation_rules = [$this->name => \FILTER_VALIDATE_INT];
                $this->type = static::TYPE_INTEGER;
                break;

            case 'decimal':
            case 'double precision':
                $this->length = (int) $ColumnInfo->numeric_precision;
                $this->precision = (int) $ColumnInfo->numeric_scale;
                $this->validation_rules = [$this->name => \FILTER_VALIDATE_FLOAT];
                $this->type = static::TYPE_FLOAT;
                break;

            case 'timestamp':
            case 'timestamp without time zone':
            case 'timestamp with time zone':
                $this->length = 19;
                $this->validation_rules = [$this->name => \FILTER_SANITIZE_STRING];
                $this->type = static::TYPE_TIMESTAMP;
                break;
            
            case 'json':
                $this->length = null;
                $this->validation_rules = [$this->name => \FILTER_SANITIZE_STRING];
                $this->type = static::TYPE_JSON;
                break;
            
            case 'boolean':
                $this->length = 1;
                $this->validation_rules = [$this->name => \FILTER_VALIDATE_BOOLEAN];
                $this->type = static::TYPE_BOOLEAN;
                break;

            case 'point':
                $this->length = null;
                $this->validation_rules = null;
                $this->type = static::TYPE_POINT;
                break;

            default:
                throw new Exception\Column('Unsupported data type: "%s"', $type);
                break;
        }
    }
}
