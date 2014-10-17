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

interface Handler extends \Everon\Interfaces\Dependency\Factory, \Everon\DataMapper\Interfaces\Dependency\DataMapperManager
{
    /**
     * @param $domain_name
     * @return Repository
     * @throws \Everon\DataMapper\Exception\Schema
     */
    function getRepositoryByName($domain_name);

    /**
     * @param $name
     * @param Repository $Repository
     */
    function setRepositoryByName($name, Repository $Repository);

    /**
     * @param $domain_name
     * @return \Everon\Domain\Interfaces\Model
     * @throws \Everon\Exception\Domain
     */
    function getModelByName($domain_name);

    /**
     * @param $name
     * @param $Model
     */
    function setModelByName($name, $Model);

    /**
     * @param $domain_name
     * @param array $data
     * @return Entity
     */
    function buildEntityFromArray($domain_name, array $data);
}