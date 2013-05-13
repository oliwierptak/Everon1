<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\View\Element;

/**
 * @method null setAction
 * @method null setMethod
 * @method null setEnctype
 */
class Page extends \Everon\View\Element
{

    public function __construct($data=null)
    {
        parent::__construct([
            'title' => '',
            'lang' => 'en-GB',
            'static_url' => 'static/',
            'charset' => 'UTF-8',
            'keywords' => 'Everon',
            'description' => 'PHP 5.4+ Framework',
            'body' => 'Hello World',
        ], $data);
    }

}