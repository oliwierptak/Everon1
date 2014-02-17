<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Rest;

use Everon\Exception;
use Everon\Rest\Interfaces;

class ApiKey implements Interfaces\ApiKey
{
    protected $id = null;

    protected $secret = null;
    
    public function __construct($id, $secret)
    {
        $this->id = $id;
        $this->secret = $secret;
    }

    /**
     * @inheritdoc
     */
    public function setSecret($secret)
    {
        $this->secret = $secret;
    }

    /**
     * @inheritdoc
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * @inheritdoc
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }
}