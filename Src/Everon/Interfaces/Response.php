<?php
namespace Everon\Interfaces;

interface Response
{
    function toHtml();
    function toJson();
    function send();
}