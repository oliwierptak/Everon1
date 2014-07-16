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


    /**
     * @inheritdoc
     */
    protected function init(array $data, array $primary_key_list, array $unique_key_list, array $foreign_key_list)
    {
        $ColumnInfo = new Helper\PopoProps($data);
        $this->name = $ColumnInfo->column_name;
        $this->is_pk = $this->hasInfo($this->name, $primary_key_list);
        $this->is_unique = $this->hasInfo($this->name, $unique_key_list) || $this->is_pk;
        $this->is_nullable = ($ColumnInfo->is_nullable == 'YES');
        $this->default = $ColumnInfo->column_default;
        $this->schema = $ColumnInfo->table_schema;
        $this->table = $ColumnInfo->__table_name_without_schema;
        
        switch ($type = $ColumnInfo->data_type) {
            case 'char':
            case 'character varying':
            case 'text':
                $this->length = (int) $ColumnInfo->character_maximum_length;
                $this->encoding = $ColumnInfo->character_set_name;
                $this->type = static::TYPE_STRING;
                $this->Validator = function($value) {
                    return is_string($value);
                };
                break;

            case 'bigint':
            case 'integer':
            case 'smallint':
                $this->length = (int) $ColumnInfo->numeric_precision;
                $this->precision = (int) $ColumnInfo->numeric_scale;
                $this->type = static::TYPE_INTEGER;
                $this->Validator = function($value) {
                    return is_numeric($value);
                };
                break;

            case 'decimal':
            case 'double precision':
                $this->length = (int) $ColumnInfo->numeric_precision;
                $this->precision = (int) $ColumnInfo->numeric_scale;
                $this->type = static::TYPE_FLOAT;
                $this->Validator = function($value) {
                    return is_float($value);
                };
                break;

            case 'timestamp':
            case 'timestamp without time zone':
            case 'timestamp with time zone':
                $this->length = 19;
                $this->type = static::TYPE_TIMESTAMP;
                $this->Validator = function($value) {
                    $dt = new \DateTime('@'.strtotime($value));
                    return $dt !== false && !array_sum($dt->getLastErrors());
                };
                break;
            
            case 'json':
                $this->length = null;
                $this->type = static::TYPE_JSON;
                $this->Validator = function($value) {
                    return is_string($value);
                };
                break;
            
            case 'boolean':
                $this->length = 1;
                $this->type = static::TYPE_BOOLEAN;
                $this->Validator = function($value) {
                    return is_string($value) && ($value === 'f' || $value === 't');
                };
                break;

            case 'point':
                $this->length = null;
                $this->type = static::TYPE_POINT;
                $this->Validator = function($value) {
                    return is_string($value);
                };
                break;
            
            case 'polygon':
                $this->length = null;
                $this->type = static::TYPE_POLYGON;
                $this->Validator = function($value) {
                    return is_string($value);
                };
                break;

            default:
                throw new Exception\Column('Unsupported data type: "%s"', $type);
                break;
        }
    }
}
