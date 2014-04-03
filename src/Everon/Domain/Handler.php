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
    public function __call($name, $arguments)
    {
        $tokens = explode('_', $this->stringCamelToUnderscore($name));
        if (count($tokens) >= 2) {
            array_shift($tokens); //remove get
            list($domain_name, $domain_type) = $tokens;

            switch ($domain_type) {
                case 'Model':
                    return $this->getModel($domain_name);
                    break;

                case 'Repository':
                    return $this->getRepository($domain_name);
                    break;
            }
        }
        
        throw new Exception\Domain('Invalid handler method: "%s"', $name);
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
            $data_mapper_name = $this->getDataMapperManager()->getDomainMapper()->getDataMapperNameByDomain($domain_name);
            $this->assertIsNull($data_mapper_name, 'Invalid data mapper relation for: "%s"', 'Domain');

            $Schema = $this->getDataMapperManager()->getSchema();
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
    public function buildEntityFromArray($domain_name, array $data)
    {
        $Repository = $this->getRepository($domain_name);
        return $Repository->buildFromArray($data);
    }
}