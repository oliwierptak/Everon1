<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Dependency;


trait Response
{

    protected $Response = null;


    /**
     * @return \Everon\Interfaces\Response
     */
    public function getResponse()
    {
        return $this->Response;
    }

    /**
     * @param \Everon\Interfaces\Response $Response
     */
    public function setResponse(\Everon\Interfaces\Response $Response)
    {
        $this->Response = $Response;
    }

}
