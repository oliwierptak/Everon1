<?php
namespace Everon\Interfaces;

interface Environment
{
    function getRoot();
    function getConfig();
    function getModel();
    function getView();
    function getViewTemplate();
    function getController();
    function getSource();
    function getTest();
    function getEveron();
    function getEveronLib();
    function getEveronInterface();
    function getTmp();
    function getCache();
    function getCacheConfig();
    function getLog();
}
