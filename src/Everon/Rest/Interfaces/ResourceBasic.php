<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Rest\Interfaces;

use Everon\Domain\Interfaces\Entity;
use Everon\Interfaces\Collection;


interface ResourceBasic extends \Everon\Interfaces\Arrayable
{
    /**
     * @param $version
     */
    function setVersion($version);

    /**
     * @inheritdoc
     */
    function getVersion();

    /**
     * @param $href
     */
    function setHref($href);

    /**
     * @inheritdoc
     */
    function getHref();

    /**
     * @return string
     */
    function toJson();
}