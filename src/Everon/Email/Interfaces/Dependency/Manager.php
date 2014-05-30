<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Email\Interfaces\Dependency;

interface Manager
{
    /**
     * @return \Everon\Email\Interfaces\Manager
     */
    function getEmailManager();

    /**
     * @param \Everon\Email\Interfaces\Manager $EmailManager
     */
    function setEmailManager(\Everon\Email\Interfaces\Manager $EmailManager);
}
