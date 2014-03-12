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
    use Helper\Asserts\IsNull;
    use Helper\Exceptions;
    

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
     * @var \Everon\Interfaces\Collection
     */
    protected $MappingCollection = null;


    /**
     * @param ConnectionManager $ConnectionManager
     * @param array $mapping
     */
    public function __construct(ConnectionManager $ConnectionManager, array $mapping)
    {
        $this->ConnectionManager = $ConnectionManager;
        $this->MappingCollection = new Helper\Collection($mapping);
    }

    /**
     * @inheritdoc
     */
    public function getModel($domain_name)
    {
        if (isset($this->models[$domain_name]) === false) {
            $this->models[$domain_name] = $this->getFactory()->buildDomainModel($domain_name);
        }

        if (isset($this->models[$domain_name]) === false) {
            throw new SchemaException('Invalid model name: "%s"', $domain_name);
        }

        return $this->models[$domain_name];
    }

    /**
     * @inheritdoc
     */
    public function getRepository($domain_name)
    {
        if (isset($this->repositories[$domain_name]) === false) {
            $data_mapper_name = $this->getDataMapperNameFromDomain($domain_name);
            $this->assertIsNull($data_mapper_name, 'Invalid data mapper relation for: "%s"', 'Domain');

            $Schema = $this->getSchema();
            $DataMapper = $this->getFactory()->buildDataMapper($domain_name, $Schema->getTable($data_mapper_name), $Schema);
            $this->repositories[$domain_name] = $this->getFactory()->buildDomainRepository($domain_name, $DataMapper);
        }
        
        if (isset($this->repositories[$domain_name]) === false) {
            throw new SchemaException('Invalid repository name: "%s"', $domain_name);
        }

        return $this->repositories[$domain_name];
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
    
    public function getDataMapperNameFromDomain($domain_name)
    {
        $key = array_search($domain_name, $this->MappingCollection->toArray());
        if ($key === false) {
            return null;
        }
        
        return $key;
    }
}