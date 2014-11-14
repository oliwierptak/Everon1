<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\DataMapper\Interfaces;

use Everon\FileSystem;
use Everon\Interfaces\PdoAdapter;

interface Schema  extends \Everon\DataMapper\Interfaces\Dependency\SchemaReader, \Everon\Domain\Interfaces\Dependency\DomainMapper 
{
    /**
     * @param $name
     * @return PdoAdapter
     * @throws \Everon\DataMapper\Exception\Schema
     */    
    function getPdoAdapterByName($name);

    /**
     * @param FileSystem\Interfaces\PhpCache $CacheLoader
     */
    function setCacheLoader(FileSystem\Interfaces\PhpCache $CacheLoader);

    /**
     * @return \Everon\FileSystem\Interfaces\PhpCache
     */
    function getCacheLoader();
    
    /**
     * @return ConnectionManager
     */
    function getConnectionManager();

    function getAdapterName();

    /**
     * @return string
     */
    function getDriver();

    function getDatabase();
    
    function getTables();

    function setTables($tables);

    /**
     * @param $name
     * @return \Everon\DataMapper\Interfaces\Schema\Table
     * @throws \Everon\DataMapper\Exception\Schema
     */
    function getTableByName($name);

    /**
     * @param string $database_locale
     */
    function setDatabaseTimezone($database_locale);

    /**
     * @return string
     */
    function getDatabaseTimezone();
}
