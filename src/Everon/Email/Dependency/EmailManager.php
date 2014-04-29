<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Email\Dependency;

/**
 * @author Zeger Hoogeboom <zeger_hoogeboom@hotmail.com>
 */
trait EmailManager 
{
    /**
     * @var \Everon\Email\Interfaces\Manager
     */
    protected $EmailManager = null;

    /**
     * @param \Everon\Email\Interfaces\Manager $EmailManager
     */
    public function setEmailManager($EmailManager)
    {
        $this->EmailManager = $EmailManager;
    }

    /**
     * @return \Everon\Email\Interfaces\Manager
     */
    public function getEmailManager()
    {
        return $this->EmailManager;
    }

} 