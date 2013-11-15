<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Interfaces;

use Everon\Exception;
use Everon\Interfaces;

/**
 * @method array toArray()
 */
interface ConfigLoaderItem
{
    function getFilename();
    function getData();
    function setData(array $data);
}
