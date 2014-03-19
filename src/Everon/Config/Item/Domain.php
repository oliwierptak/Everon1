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
    /**
     * @var string
     */
    protected $id_field = null;

    /**
     * @var string
     */
    protected $domain = null;
    
    /**
     * @var array
     */
    protected $connection = null;

    
    public function __construct(array $data)
    {
        parent::__construct($data, [
            'id_field' => null,
            'domain' => null,
            'connection' => []
        ]);
    }

    protected function init()
    {
        parent::init();
        
        $this->setIdField($this->data['id_field']);
        $this->setDomain($this->data['domain']);
        $this->setConnections($this->data['connection']);
    }

    /**
     * @param array $connections
     */
    public function setConnections($connections)
    {
        $this->connection = $connections;
    }

    /**
     * @return array
     */
    public function getConnections()
    {
        return $this->connection;
    }

    /**
     * @param string $id_field
     */
    public function setIdField($id_field)
    {
        $this->id_field = $id_field;
    }

    /**
     * @return string
     */
    public function getIdField()
    {
        return $this->id_field;
    }

    /**
     * @param string $domain
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }  
}