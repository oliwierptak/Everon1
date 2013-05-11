<?php
namespace Everon\Interfaces;


interface ConfigItemRouter
{
    function getName();
    function setName($route_name);
    function getUrl();
    function setUrl($url);
    function getController();
    function setController($controller);
    function getAction();
    function setAction($action);
    function isDefault();
    function setIsDefault($is_default);

    function getGetRegex();
    function setGetRegex($regex);
    function getPostRegex();
    function setPostRegex($regex);

    function validateQueryAndGet($url, array $get_data);
    function validatePost(array $post_data);

    function matchesByUrl($request_url);
    
    function toArray();
}