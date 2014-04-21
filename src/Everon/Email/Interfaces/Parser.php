<?php
/**
 * Created by PhpStorm.
 * User: Zeger
 * Date: 4/21/14
 * Time: 8:51 PM
 */

namespace Everon\Email\Interfaces;


interface Parser {

    /**
     * @param Template $Template
     * @return string
     */
    function parse(Template $Template);
}