<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Config\Item;

use Everon\Config;

class Domain extends Config\Item implements Config\Interfaces\ItemDomain
{
    const TYPE_TABLE = 'table';
    const TYPE_VIEW = 'view';
    const TYPE_MAT_VIEW = 'mat_view';
    
    /**
     * @var string
     */
    protected $id_field = null;

    /**
     * @var string
     */
    protected $table = null;

    /**
     * @var string
     */
    protected $table_original = null;
    
    /**
     * @var array
     */
    protected $connection = null;

    /**
     * @var string
     */
    protected $type = null;

    /**
     * @var array
     */
    protected $columns = null;

    /**
     * @var array
     */
    protected $primary_keys = null;

    /**
     * @var array
     */
    protected $foreign_keys = null;

    /**
     * @var array
     */
    protected $nullable = null;

    
    public function __construct(array $data)
    {
        parent::__construct($data, [
            'id_field' => null,
            'table' => null,
            'table_original' => null,
            'type' => static::TYPE_TABLE,
            'connection' => [],
            'columns' => [],
            'primary_keys' => [],
            'foreign_keys' => [],
            'nullable' => []
        ]);
    }

    protected function init()
    {
        parent::init();
        
        $this->setIdField($this->data['id_field']);
        $this->setTable($this->data['table']);
        $this->setTableOriginal($this->data['table_original']);
        $this->setConnections($this->data['connection']);
        $this->setType($this->data['type']);
        $this->setColumns($this->data['columns']);
        $this->setPrimaryKeys($this->data['primary_keys']);
        $this->setForeignKeys($this->data['foreign_keys']);
        $this->setNullable($this->data['nullable']);
    }

    /**
     * @inheritdoc
     */
    public function setConnections($connections)
    {
        $this->connection = $connections;
    }

    /**
     * @inheritdoc
     */
    public function getConnections()
    {
        return $this->connection;
    }

    /**
     * @inheritdoc
     */
    public function setIdField($id_field)
    {
        $this->id_field = $id_field;
    }

    /**
     * @inheritdoc
     */
    public function getIdField()
    {
        return $this->id_field;
    }

    /**
     * @inheritdoc
     */
    public function setTable($table)
    {
        $this->table = $table;
    }

    /**
     * @inheritdoc
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @inheritdoc
     */
    public function setTableOriginal($table_original)
    {
        $this->table_original = $table_original;
    }

    /**
     * @inheritdoc
     */
    public function getTableOriginal()
    {
        return $this->table_original;
    }

    /**
     * @inheritdoc
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @inheritdoc
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @inheritdoc
     */
    public function setPrimaryKeys($primary_keys)
    {
        $this->primary_keys = $primary_keys;
    }

    /**
     * @inheritdoc
     */
    public function getPrimaryKeys()
    {
        return $this->primary_keys;
    }

    /**
     * @param array $foreign_keys
     */
    public function setForeignKeys(array $foreign_keys)
    {
        $this->foreign_keys = $foreign_keys;
    }

    /**
     * @return array
     */
    public function getForeignKeys()
    {
        return $this->foreign_keys;
    }
    
    /**
     * @inheritdoc
     */
    public function setColumns($columns)
    {
        $this->columns = $columns;
    }

    /**
     * @inheritdoc
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @inheritdoc
     */
    public function setNullable(array $nullable)
    {
        $this->nullable = $nullable;
    }

    /**
     * @inheritdoc
     */
    public function getNullable()
    {
        return $this->nullable;
    }
}