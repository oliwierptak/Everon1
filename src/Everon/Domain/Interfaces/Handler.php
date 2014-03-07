<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Domain\Interfaces;

use Everon\DataMapper\Interfaces\ConnectionManager;
use Everon\DataMapper\Interfaces\Schema;
use Everon\Interfaces\Factory;

interface Handler
{
    /**
     * @return ConnectionManager
     */
    function getConnectionManager();

    /**
     * @param ConnectionManager $ConnectionManager
     */
    function setConnectionManager(ConnectionManager $ConnectionManager);

    /**
     * @param Repository $Repository
     * @param $id
     * @param array $data
     * 
     */
    function buildEntity(Repository $Repository, $id, array $data);

    /**
     * @return Schema
     */
    function getSchema();

    /**
     * @param $name
     * @return Repository
     * @throws \Everon\DataMapper\Exception\Schema
     */
    function getRepository($name);

    /**
     * @param $name
     * @return mixed
     */
    function getModel($name);

    /**
     * @param Factory $Factory
     */
    function setFactory(Factory $Factory);

    /**
     * @return Factory
     */
    function getFactory();
}