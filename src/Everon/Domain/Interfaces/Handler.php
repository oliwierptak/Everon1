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

use Everon\DataMapper\Interfaces\Schema;

interface Handler extends \Everon\Interfaces\Dependency\Factory, \Everon\DataMapper\Interfaces\Dependency\DataMapperManager
{
    /**
     * @param $domain_name
     * @return Repository
     * @throws \Everon\DataMapper\Exception\Schema
     */
    function getRepository($domain_name);

    /**
     * @param $domain_name
     * @return mixed
     */
    function getModel($domain_name);

    /**
     * @param $domain_name
     * @param array $data
     * @return Entity
     */
    function buildEntityFromArray($domain_name, array $data);
}