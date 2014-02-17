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

trait ApiKey
{

    protected $ApiKey = null;


    /**
     * @return \Everon\Rest\Interfaces\ApiKey
     */
    public function getApiKey()
    {
        return $this->ApiKey;
    }

    /**
     * @param \Everon\Rest\Interfaces\ApiKey $ApiKey
     */
    public function setApiKey(\Everon\Rest\Interfaces\ApiKey $ApiKey)
    {
        $this->ApiKey = $ApiKey;
    }

}
