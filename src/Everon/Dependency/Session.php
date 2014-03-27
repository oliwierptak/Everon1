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


trait Session
{

    /**
     * @var \Everon\Http\Interfaces\Session
     */
    protected $Session = null;


    /**
     * @return \Everon\Http\Interfaces\Session
     */
    public function getSession()
    {
        return $this->Session;
    }

    /**
     * @param \Everon\Http\Interfaces\Session $Session
     */
    public function setSession(\Everon\Http\Interfaces\Session $Session)
    {
        $this->Session = $Session;
    }

}
