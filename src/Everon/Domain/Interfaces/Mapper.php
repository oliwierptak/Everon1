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

use Everon\Interfaces;

interface Mapper extends Interfaces\Arrayable
{
    /**
     * @param $domain_name
     * @return string|null
     */
    function getByDomainName($domain_name);

    /**
     * @param $data_mapper_name
     * @return string|null
     */
    function getByDataMapperName($data_mapper_name);
}
