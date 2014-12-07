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
    use Dependency\Injection\ConfigManager;
    use Dependency\Injection\Factory;
    use Dependency\Injection\FileSystem;
    
    use DataMapper\Dependency\ConnectionManager;
    use DataMapper\Dependency\Schema;
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
            $CacheLoader = $this->getFactory()->buildFileSystemCacheLoader('Serialized', $this->getFileSystem()->getRealPath('//Tmp/cache/data_mapper/'));
                
            $this->Schema = $this->getFactory()->buildSchema(
                $SchemaReader, $this->getConnectionManager(), $this->getDomainMapper(), $CacheLoader
            );
            
            //$this->Schema->saveTablesToCache();
            
            if ($this->getConfigManager()->getConfigValue('everon.cache.data_mapper')) {
                $this->Schema->loadTablesFromCache();
            }
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