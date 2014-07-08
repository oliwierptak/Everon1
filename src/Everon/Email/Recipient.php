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

use Everon\Helper;

/**
 * @author Zeger Hoogeboom <zeger_hoogeboom@hotmail.com>
 * @author Oliwier Ptak <oliwierptak@gmail.com>
 */
class Recipient implements Interfaces\Recipient
{
    use Helper\ToArray;
    
    /**
     * @var array
     */
    protected $to = null;

    /**
     * @var array
     */
    protected $cc = null;

    /**
     * @var array
     */
    protected $bcc;


    /**
     * @param array $to array of Interfaces\Address
     * @param array $cc array of Interfaces\Address
     * @param array $bcc array of Interfaces\Address
     */
    function __construct(array $to, array $cc=[], array $bcc=[])
    {
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
     * @param array $to
     */
    public function setTo(array $to)
    {
        $this->to = $to;
    }

    /**
     * @return array
     */
    public function getTo()
    {
        return $this->to;
    }
    
    protected function getToArray()
    {
        return [
            'to' => (new Helper\Collection($this->to))->toArray(true),
            'cc' => (new Helper\Collection($this->cc))->toArray(true),
            'bcc' => (new Helper\Collection($this->bcc))->toArray(true)
        ];
    }
} 