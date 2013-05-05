<?php
namespace Everon\Interfaces;

interface Response
{
    function toHtml();
    function toJson();
    function send();
    function setData($data);
    function getData();
    function setResult($result);
    function getResult();

}