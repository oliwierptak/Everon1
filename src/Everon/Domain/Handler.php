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

use Everon\DataMapper\Criteria;
use Everon\DataMapper\Dependency;
use Everon\DataMapper\Interfaces\ConnectionManager;
use Everon\DataMapper\Interfaces\Schema;
use Everon\DataMapper\Exception\Schema as SchemaException;
use Everon\Dependency\Injection\Factory as FactoryInjection;
use Everon\Dependency\Injection\Request as RequestInjection; //todo Domain should have no clue about Request
use Everon\Exception;
use Everon\Helper;

abstract class Handler implements Interfaces\Handler
{
    use FactoryInjection;
    use RequestInjection;

    use Dependency\ConnectionManager;
    use Helper\IsCallable;
    

    /**
     * @var array
     */
    protected $models = null;

    /**
     * @var array
     */
    protected $repositories = null;

    /**
     * @var Schema
     */
    protected $Schema = null;


    /**
     * @param ConnectionManager $ConnectionManager
     */
    public function __construct(ConnectionManager $ConnectionManager)
    {
        $this->ConnectionManager = $ConnectionManager;
    }
    
    /**
     * @inheritdoc
     */
    public function buildEntity(Interfaces\Repository $Repository, $id, array $data)
    {
        $id = $id ?: $Repository->getMapper()->getAndValidateId($data);
        $data[$Repository->getMapper()->getTable()->getPk()] = $id;
        
        $Criteria = new Criteria();
        $Criteria->limit($this->getRequest()->getGetParameter('limit', 10)); //todo remove request dependency from domain
        $Criteria->offset($this->getRequest()->getGetParameter('offset', 0));

        $Entity = $this->getFactory()->buildDomainEntity($Repository->getName(), $id, $data);
        $Repository->buildEntityRelations($Entity, $Criteria);
        
        return $Entity;
    }

    /**
     * @inheritdoc
     */
    public function getModel($name)
    {
        if (isset($this->models[$name]) === false) {
            $this->models[$name] = $this->getFactory()->buildDomainModel($name);
        }

        if (isset($this->models[$name]) === false) {
            throw new SchemaException('Invalid model name: "%s"', $name);
        }

        return $this->models[$name];
    }

    /**
     * @inheritdoc
     */
    public function getRepository($name)
    {
        if (isset($this->repositories[$name]) === false) {
            $Schema = $this->getSchema();
            $DataMapper = $this->getFactory()->buildDataMapper(
                $Schema->getTable($name), $Schema
            );
            $this->repositories[$name] = $this->getFactory()->buildDomainRepository($name, $DataMapper);
        }
        
        if (isset($this->repositories[$name]) === false) {
            throw new SchemaException('Invalid repository name: "%s"', $name);
        }

        return $this->repositories[$name];
    }

    /**
     * @inheritdoc
     */
    public function getSchema()
    {
        if ($this->Schema === null) {
            $Connection = $this->getConnectionManager()->getConnectionByName('schema');
            list($dsn, $username, $password, $options) = $Connection->toPdo();
            $Pdo = $this->getFactory()->buildPdo($dsn, $username, $password, $options);
            $PdoAdapter = $this->getFactory()->buildPdoAdapter($Pdo, $Connection);
            $SchemaReader = $this->getFactory()->buildSchemaReader($PdoAdapter);
            $this->Schema = $this->getFactory()->buildSchema($SchemaReader, $this->getConnectionManager());
        }

        return $this->Schema;
    }
}