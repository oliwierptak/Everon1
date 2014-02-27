<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Rest\Resource;

use Everon\Domain\Interfaces\Entity;

abstract class Domain extends \Everon\Rest\Resource
{
    /**
     * @var Entity
     */
    protected $ResourceEntity = null;


    public function __construct($name, $version, Entity $Entity)
    {
        parent::__construct($name, $version);
        $this->ResourceEntity = $Entity;
    }
    
    protected function init()
    {
        if ($this->data === null) {
            $this->data = $this->ResourceEntity->toArray();
        }
    }

}
