<?php
namespace Everon\View;


abstract class Element extends \Everon\Helper\Popo implements \Everon\Interfaces\Component
{
    /**
     * @param array $defaults
     * @param mixed $data
     */
    public function __construct($defaults, $data=null)
    {
        $data = (!is_array($data)) ? [] : $data;
        $data = array_merge($defaults, $data);

        parent::__construct($data);
    }
}