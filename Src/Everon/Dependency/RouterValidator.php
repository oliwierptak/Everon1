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


trait RouterValidator
{

    protected $RouterValidator = null;


    /**
     * @return \Everon\Interfaces\RouterValidator
     */
    public function getRouterValidator()
    {
        return $this->RouterValidator;
    }

    /**
     * @param \Everon\Interfaces\RouterValidator $RouterValidator
     */
    public function setRouterValidator(\Everon\Interfaces\RouterValidator $RouterValidator)
    {
        $this->RouterValidator = $RouterValidator;
    }

}
