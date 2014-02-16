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

interface Response extends \Everon\Http\Interfaces\Response
{
    /**
     * @return bool
     */
    function isError();

    /**
     * @return bool
     */
    function isClientError();

    /**
     * @return bool
     */
    function isServerError();
}