<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\View\Html;

/**
 * @method null setAction
 * @method null setMethod
 * @method null setEnctype
 */
class Form extends \Everon\View\Element
{

    public function __construct($data=null)
    {
        parent::__construct([
            'action' => '',
            'method' => 'post',
            'enctype' => 'multipart/form-data',
        ], $data);
    }

}