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

class Address implements Interfaces\Address
{
    use Helper\ToArray;
    
    /**
     * @var string
     */
    protected $name = null;

    /**
     * @var string
     */
    protected $email = null;


    /**
     * @param $name
     * @param $email
     */
    public function __construct($email, $name)
    {
        $this->email = $email;
        $this->name = $name;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
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
    
    protected function getToArray()
    {
        return [
            'email' => $this->getEmail(),
            'name' => $this->getName()
        ];
    }
}