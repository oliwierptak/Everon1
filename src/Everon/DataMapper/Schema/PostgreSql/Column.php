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
    protected function init()
    {
        if ($this->initialized) {
            return;
        }
        
        $this->name = $this->ColumnInfo->column_name;
        $this->is_nullable = ($this->ColumnInfo->is_nullable == 'YES');
        $this->default = $this->ColumnInfo->column_default;
        $this->schema = $this->ColumnInfo->table_schema;
        $this->table = $this->ColumnInfo->__table_name_without_schema;
        
        switch ($type = $this->ColumnInfo->data_type) {
            case 'char':
            case 'character varying':
            case 'text':
                $this->length = (int) $this->ColumnInfo->character_maximum_length;
                $this->encoding = $this->ColumnInfo->character_set_name;
                $this->type = static::TYPE_STRING;
                $this->Validator = $this->getFactory()->buildSchemaValidator('String', 'PostgreSql');
                break;

            case 'bigint':
            case 'integer':
            case 'smallint':
                $this->length = (int) $this->ColumnInfo->numeric_precision;
                $this->precision = (int) $this->ColumnInfo->numeric_scale;
                $this->type = static::TYPE_INTEGER;
                $this->Validator = $this->getFactory()->buildSchemaValidator('Numeric', 'PostgreSql');
                break;

            case 'decimal':
            case 'double precision':
                $this->length = (int) $this->ColumnInfo->numeric_precision;
                $this->precision = (int) $this->ColumnInfo->numeric_scale;
                $this->type = static::TYPE_FLOAT;
                $this->Validator = $this->getFactory()->buildSchemaValidator('Float', 'PostgreSql');
                break;

            case 'timestamp':
            case 'timestamp without time zone':
            case 'timestamp with time zone':
                $this->length = 19;
                $this->type = static::TYPE_TIMESTAMP;
                $this->Validator = $this->getFactory()->buildSchemaValidator('Timestamp', 'PostgreSql');
                break;
            
            case 'json':
                $this->length = null;
                $this->type = static::TYPE_JSON;
                $this->Validator = $this->getFactory()->buildSchemaValidator('String', 'PostgreSql');
                break;
            
            case 'boolean':
                $this->length = 1;
                $this->type = static::TYPE_BOOLEAN;
                $this->Validator = $this->getFactory()->buildSchemaValidator('Boolean', 'PostgreSql');
                break;

            case 'point':
                $this->length = null;
                $this->type = static::TYPE_POINT;
                $this->Validator = $this->getFactory()->buildSchemaValidator('String', 'PostgreSql');
                break;
            
            case 'polygon':
                $this->length = null;
                $this->type = static::TYPE_POLYGON;
                $this->Validator = $this->getFactory()->buildSchemaValidator('String', 'PostgreSql');
                break;

            default:
                throw new Exception\Column('Unsupported data type: "%s"', $type);
                break;
        }
        
        $this->initialized = true;
    }
}
