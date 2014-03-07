<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Rest\Dependency;


trait Request
{
    /**
     * @var \Everon\Rest\Interfaces\Request
     */
    protected $Request = null;


    /**
     * @return \Everon\Rest\Interfaces\Request
     */
    public function getRequest()
    {
        return $this->Request;
    }

    public function setRequest(\Everon\Rest\Interfaces\Request $Request)
    {
        $this->Request = $Request;
    }

}
