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
use Everon\DataMapper\Exception\Schema as SchemaException;
use Everon\Dependency\Injection\Factory as FactoryInjection;
use Everon\Exception;
use Everon\Helper;

abstract class Handler implements Interfaces\Handler
{
    use FactoryInjection;
    use Dependency\Injection\DataMapperManager;

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
     * @var Interfaces\Mapper
     */
    protected $DomainMapper = null;


    public function __construct(Interfaces\Mapper $DomainMapper)
    {
        $this->DomainMapper = $DomainMapper;
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
            $data_mapper_name = $this->DomainMapper->getDataMapperNameByDomain($domain_name);
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