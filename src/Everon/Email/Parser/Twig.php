<?php
/**
 * Created by PhpStorm.
 * User: Zeger
 * Date: 4/21/14
 * Time: 8:52 PM
 */

namespace Everon\Email\Parser;


use Everon\Email\Interfaces\Parser;
use Everon\Email\Interfaces\Template;

class Twig implements Parser {

    /**
     * @inheritdoc
     */
    function parse(Template $Template)
    {
        return "parsed by twig";
    }


} 