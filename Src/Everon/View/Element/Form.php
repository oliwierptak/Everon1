<?php
namespace Everon\View\Element;

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