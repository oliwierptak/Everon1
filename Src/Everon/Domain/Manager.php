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
use Everon\Dependency;
use Everon\DataMapper\Interfaces\Schema;
use Everon\DataMapper\Interfaces\Schema\Reader;
use Everon\Domain\Interfaces\Repository;

abstract class Manager implements Interfaces\Manager
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
            $Reader = $this->getFactory()->buildSchemaReader($Connection);
            $this->schemas[$name] = $this->getFactory()->buildSchema($Connection->getName(), $Reader, $this->getConnectionManager());
        }

        return $this->schemas[$name];        
    }

}