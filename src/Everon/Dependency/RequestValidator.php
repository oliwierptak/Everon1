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


trait RequestValidator
{

    /**
     * @var \Everon\Interfaces\RequestValidator
     */
    protected $RequestValidator = null;


    /**
     * @return \Everon\Interfaces\RequestValidator
     */
    public function getRequestValidator()
    {
        return $this->RequestValidator;
    }

    /**
     * @param \Everon\Interfaces\RequestValidator $RequestValidator
     */
    public function setRequestValidator(\Everon\Interfaces\RequestValidator $RequestValidator)
    {
        $this->RequestValidator = $RequestValidator;
    }

}
