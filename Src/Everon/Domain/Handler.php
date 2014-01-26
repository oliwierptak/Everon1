<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Domain;

use Everon\DataMapper\Interfaces\ConnectionManager;
use Everon\DataMapper\Interfaces\Schema;
use Everon\Dependency;
use Everon\Domain\Interfaces\Repository;

abstract class Handler implements Interfaces\Handler
{
    use Dependency\Injection\Factory;
    use Dependency\DataMapper\ConnectionManager;

    /**
     * @var array
     */
    protected $models = null;

    /**
     * @var array
     */
    protected $repositories = null;

    /**
     * @var array
     */
    protected $schemas = null;


    public function __construct(ConnectionManager $ConnectionManager)
    {
        $this->ConnectionManager = $ConnectionManager;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getModel($name)
    {
        if (isset($this->models[$name]) === false) {
            $this->models[$name] = $this->getFactory()->buildDomainModel($name);
        }

        return $this->models[$name];
    }
    
    /**
     * @param $name
     * @return Repository
     */
    public function getRepository($name)
    {
        if (isset($this->repositories[$name]) === false) {
            $DataMapper = $this->getFactory()->buildDataMapper($name, $this->getSchema($name));
            $this->repositories[$name] = $this->getFactory()->buildDomainRepository($name, $DataMapper);
        }

        return $this->repositories[$name];
    }

    /**
     * @param $name
     * @return Schema
     */
    public function getSchema($name)
    {
        if (isset($this->schemas[$name]) === false) {
            $Connection = $this->getConnectionManager()->getConnectionByName('schema');
            list($dsn, $username, $password, $options) = $Connection->toPdo();
            $Pdo = $this->getFactory()->buildPdo($dsn, $username, $password, $options);
            $PdoAdapter = $this->getFactory()->buildPdoAdapter($Pdo, $Connection);
            $SchemaReader = $this->getFactory()->buildSchemaReader($Connection, $PdoAdapter);
            $this->schemas[$name] = $this->getFactory()->buildSchema($SchemaReader, $this->getConnectionManager());
        }

        return $this->schemas[$name];        
    }

}