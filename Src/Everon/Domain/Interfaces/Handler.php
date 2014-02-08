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
     * @param $name
     * @param $id
     * @param array $data
     */
    function getEntity($name, $id, array $data);

    /**
     * @param $name
     * @return Schema
     */
    function getSchema($name);

    /**
     * @param $name
     * @return Repository
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