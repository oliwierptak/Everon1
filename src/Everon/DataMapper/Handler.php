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

use Everon\DataMapper;
use Everon\Dependency;
use Everon\Domain;
use Everon\Helper;

abstract class Handler implements Interfaces\Handler
{
    use DataMapper\Dependency\ConnectionManager;
    use DataMapper\Dependency\Schema;
    use Dependency\Injection\Factory;
    use Domain\Dependency\DomainMapper;
    
    /**
     * @param DataMapper\Interfaces\ConnectionManager $ConnectionManager
     * @param Domain\Interfaces\Mapper $DomainMapper
     */
    public function __construct(DataMapper\Interfaces\ConnectionManager $ConnectionManager, Domain\Interfaces\Mapper $DomainMapper)
    {
        $this->ConnectionManager = $ConnectionManager;
        $this->DomainMapper = $DomainMapper;
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
            $this->Schema = $this->getFactory()->buildSchema($SchemaReader, $this->getConnectionManager(), $this->getDomainMapper());
        }

        return $this->Schema;
    }

    /**
     * @param Interfaces\Schema $Schema
     */
    public function setSchema(DataMapper\Interfaces\Schema $Schema)
    {
        $this->Schema = $Schema;
    }
}