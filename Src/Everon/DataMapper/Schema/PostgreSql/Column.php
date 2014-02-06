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
        $this->is_unique = $this->hasInfo($this->name, $unique_key_list);
        $this->is_nullable = ($ColumnInfo->is_nullable == 'YES');
        $this->default = $ColumnInfo->column_default;
        $this->schema = $ColumnInfo->table_schema;
        
        switch ($type = $ColumnInfo->data_type) {
            case 'text':
            case 'character varying':
                $this->length = (int) $ColumnInfo->character_maximum_length;
                $this->encoding = $ColumnInfo->character_set_name;
                $this->validation_rules = [$this->name => \FILTER_SANITIZE_STRING];
                $this->type = static::TYPE_STRING;
                break;

            case 'integer':
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
