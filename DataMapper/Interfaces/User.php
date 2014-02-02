<?php
namespace Everon\DataMapper\Interfaces;


interface User
{
    function fetchOneByLogin($login);
}