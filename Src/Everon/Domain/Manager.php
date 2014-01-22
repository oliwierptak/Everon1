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

use Everon\Dependency;
use Everon\Interfaces;
use Everon\DataMapper\Interfaces\Schema\Reader;
use Everon\Domain\Interfaces\Repository;
use Everon\DataMapper\Interfaces\ConnectionManager;

abstract class Manager implements Interfaces\DomainManager
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
     * @var ConnectionManager
     */
    protected $ConnectionManager = null;

    /**
     * @var Reader
     */
    protected $Reader = null;


    public function __construct(Reader $Reader, ConnectionManager $Manager)
    {
        $this->Reader = $Reader;
        $this->ConnectionManager = $Manager;
    }

    /**
     * @param Reader $Reader
     */
    public function setReader(Reader $Reader)
    {
        $this->Reader = $Reader;
    }

    /**
     * @return Reader
     */
    public function getReader()
    {
        return $this->Reader;
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
        if (isset($this->models[$name]) === false) {
            $Connection = $this->getConnectionManager()->getConnectionByName('schema');
            $Reader = $this->getFactory()->buildSchemaReader($Connection);
            $Schema = $this->getFactory()->buildSchema($Connection->getName(), $Reader, $this->getConnectionManager());
            //$SchemaTable = $this->getFactory()->buildSchemaTable($name, $Schema->getColumns(), $constraints, $foreign_keys);
            $PdoAdapter = $this->getFactory()->buildDataMapper($name, $Schema);
            $this->models[$name] = $this->getFactory()->buildDomainRepository($name, $PdoAdapter);
        }

        return $this->models[$name];
    }    

}