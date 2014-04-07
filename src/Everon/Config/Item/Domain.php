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

    
    public function __construct(array $data)
    {
        parent::__construct($data, [
            'id_field' => null,
            'table' => null,
            'type' => static::TYPE_TABLE,
            'connection' => [],
            'columns' => [],
            'primary_keys' => []
        ]);
    }

    protected function init()
    {
        parent::init();
        
        $this->setIdField($this->data['id_field']);
        $this->setTable($this->data['table']);
        $this->setConnections($this->data['connection']);
        $this->setType($this->data['type']);
        $this->setColumns($this->data['columns']);
        $this->setPrimaryKeys($this->data['primary_keys']);
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
    
}