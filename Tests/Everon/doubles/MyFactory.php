<?php
namespace Everon\Test;

//todo: remove this classs, remove $ns from Factory

class MyFactory extends \Everon\Factory
{

    public function buildController($class_name, \Everon\Interfaces\View $View, \Everon\Interfaces\ModelManager $ModelManager, $ns='\Everon\Controller')
    {
        return parent::buildController($class_name, $View, $ModelManager, '\Everon\Test');
    }

    public function buildModel($class_name, $ns='\Everon\Model')
    {
        return parent::buildModel($class_name, '\Everon\Test');
    }

    public function buildView($class_name, array $compilers_to_init)
    {
        return parent::buildView('MyView', $compilers_to_init, '\Everon\Test');
    }

}