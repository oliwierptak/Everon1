<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\FileSystem\Interfaces;

use Everon\Exception;

interface TmpFile
{
    /**
     * @inheritdoc
     */
    function write($content);
    function getFilename();
    function close();
}