<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\DataMapper;

class SqlPart implements Interfaces\SqlPart
{
    /**
     * @var string
     */
    protected $sql = null;

    /**
     * @var array
     */
    protected $parameters = null;

    
    public function __construct($sql, $parameters)
    {
        $this->sql = $sql;
        $this->parameters = $parameters;
    }

    /**
     * @inheritdoc
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @inheritdoc
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @inheritdoc
     */
    public function getSql()
    {
        return $this->sql;
    }

    /**
     * @inheritdoc
     */
    public function setSql($sql)
    {
        $this->sql = $sql;
    }
    
}