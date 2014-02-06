<?php
namespace Everon\DataMapper\Interfaces;


interface User extends \Everon\Interfaces\DataMapper
{
    function fetchOneByEmail($login);
}