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
use Everon\DataMapper\Interfaces\Schema;
use Everon\DataMapper\Interfaces\Manager as DataMapperManager;
use Everon\DataMapper\Exception\Schema as SchemaException;
use Everon\Dependency\Injection\Factory as FactoryInjection;
use Everon\Exception;
use Everon\Helper;

abstract class Handler implements Interfaces\Handler
{
    use FactoryInjection;
    use Dependency\DataMapperManager;

    use Helper\IsCallable;
    use Helper\Asserts\IsNull;
    use Helper\Exceptions;
    use Helper\String\CamelToUnderscore;
    

    /**
     * @var array
     */
    protected $models = null;

    /**
     * @var array
     */
    protected $repositories = null;


    /**
     * @param DataMapperManager $Manager
     */
    public function __construct(DataMapperManager $Manager)
    {
        $this->DataMapperManager = $Manager;
    }

    /**
     * @param $name
     * @param $arguments
     * @return Interfaces\Repository|mixed
     * @throws \Everon\DataMapper\Exception\Schema
     * @throws \Everon\Exception\Domain
     */
    public function __call($name, $arguments) //todo remove me
    {
        $tokens = explode('_', $this->stringCamelToUnderscore($name));
        if (count($tokens) >= 2) {
            $action = array_shift($tokens);
            $domain_type = array_pop($tokens); //remove Model or Repository
            $domain_name = implode('', $tokens);
            
            if (strcasecmp($action, 'set') === 0) {
                $action = $action.$domain_type;
                $this->$action($arguments[0], $arguments[1]);
                return null;
            }
            else if (strcasecmp($action, 'get') === 0) {
                switch ($domain_type) {
                    case 'Model':
                        return $this->getModelByName($domain_name);
                        break;

                    case 'Repository':
                        return $this->getRepositoryByName($domain_name);
                        break;
                }
            }
        }
        
        throw new Exception\Domain('Invalid handler method: "%s"', $name);
    }

    /**
     * @inheritdoc
     */
    public function getModelByName($domain_name)
    {
        if (isset($this->models[$domain_name]) === false) {
            $this->models[$domain_name] = $this->getFactory()->buildDomainModel($domain_name);
        }

        if (isset($this->models[$domain_name]) === false) {
            throw new Exception\Domain('Invalid model name: "%s"', $domain_name);
        }

        return $this->models[$domain_name];
    }

    /**
     * @inheritdoc
     */
    public function setModelByName($name, $Model)
    {
        $this->repositories[$name] = $Model;
    }

    /**
     * @inheritdoc
     */
    public function getRepositoryByName($domain_name)
    {
        if (isset($this->repositories[$domain_name]) === false) {
            $data_mapper_name = $this->getDataMapperManager()->getDomainMapper()->getTableName($domain_name);
            $this->assertIsNull($data_mapper_name, 'Invalid mapper definition for domain: "'.$domain_name.'"', 'Everon\Domain\Exception\Mapper');
            
            $Schema = $this->getDataMapperManager()->getSchema();
            $DataMapper = $this->getFactory()->buildDataMapper($domain_name, $Schema->getTableByName($data_mapper_name), $Schema);
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
    public function setRepositoryByName($name, Interfaces\Repository $Repository)
    {
        $this->repositories[$name] = $Repository;
    }

    /**
     * @inheritdoc
     */
    public function buildEntityFromArray($domain_name, array $data)
    {
        $Repository = $this->getRepositoryByName($domain_name);
        return $Repository->buildFromArray($data);
    }
}