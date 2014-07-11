<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\View\Template\Compiler;

use Everon\View\Interfaces;

class Context implements Interfaces\TemplateCompilerContext
{
    protected $compiled = null;
    protected $php = null;
    protected $scope_name = 'Tpl';
    
    /** 
     * @var mixed 
     */
    protected $Scope = null;

    /**
     * @var array|null
     */
    protected $data = null;


    public function setScopeName($name)
    {
        $this->scope_name = $name;
    }

    public function setCompiled($compiled)
    {
        $this->compiled = $compiled;
    }
    
    public function setPhp($php)
    {
        $this->php = $php;
    }
    
    public function setData(array $data)
    {
        $this->data = $data;
    }
    
    public function getCompiled()
    {
        return $this->compiled;
    }
    
    public function getPhp()
    {
        return $this->php;
    }
    
    public function getData()
    {
        return $this->data;
    }
    
    public function getScopeName()
    {
        return $this->scope_name;
    }

    /**
     * @param mixed $Scope
     */
    public function setScope($Scope)
    {
        $this->Scope = $Scope;
    }

    /**
     * @return mixed
     */
    public function getScope()
    {
        return $this->Scope;
    }
}