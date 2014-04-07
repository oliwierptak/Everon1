<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Http\Dependency;


trait HttpSession
{

    /**
     * @var \Everon\Http\Interfaces\Session
     */
    protected $HttpSession = null;


    /**
     * @return \Everon\Http\Interfaces\Session
     */
    public function getHttpSession()
    {
        return $this->HttpSession;
    }

    /**
     * @param \Everon\Http\Interfaces\Session $HttpSession
     */
    public function setHttpSession(\Everon\Http\Interfaces\Session $HttpSession)
    {
        $this->HttpSession = $HttpSession;
    }

}
