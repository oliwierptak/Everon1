<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Email;

/**
 * @author Zeger Hoogeboom <zeger_hoogeboom@hotmail.com>
 * @author Oliwier Ptak <oliwierptak@gmail.com>
 */
class Recipient implements Interfaces\Recipient
{
    
    protected $name = null;
    
    protected $to = null;

    /**
     * @var array
     */
    protected $cc = null;

    /**
     * @var array
     */
    protected $bcc;
    

    function __construct($name, $to, array $cc=[], array $bcc=[])
    {
        $this->name = $name;
        $this->to = $to;
        $this->cc = $cc;
        $this->bcc = $bcc;
    }

    /**
     * @param array $bcc
     */
    public function setBcc(array $bcc)
    {
        $this->bcc = $bcc;
    }

    /**
     * @return array
     */
    public function getBcc()
    {
        return $this->bcc;
    }

    /**
     * @param array $cc
     */
    public function setCc(array $cc)
    {
        $this->cc = $cc;
    }

    /**
     * @return array
     */
    public function getCc()
    {
        return $this->cc;
    }

    /**
     * @param string $to
     */
    public function setTo($to)
    {
        $this->to = $to;
    }

    /**
     * @return string
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
} 