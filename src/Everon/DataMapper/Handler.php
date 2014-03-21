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

use Everon\Dependency\Injection\Factory as FactoryDependency;
use Everon\DataMapper\Dependency;
use Everon\DataMapper\Interfaces\ConnectionManager;
use Everon\DataMapper\Interfaces\Schema;
use Everon\Domain\Dependency\DomainMapper as DomainMapperDependency;
use Everon\Helper;
use Everon\Domain\Interfaces\Mapper as DomainMapper;

abstract class Handler implements Interfaces\Handler
{
    use Dependency\ConnectionManager;
    use Dependency\Schema;
    use FactoryDependency;
    use DomainMapperDependency;
    
    /**
     * @param ConnectionManager $ConnectionManager
     * @param DomainMapper $DomainMapper
     */
    public function __construct(ConnectionManager $ConnectionManager, DomainMapper $DomainMapper)
    {
        $this->ConnectionManager = $ConnectionManager;
        $this->DomainMapper = $DomainMapper;
    }
    
    /**
     * @inheritdoc
     */

    /**
     * @return Schema
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