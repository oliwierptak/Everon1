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

use Everon\Interfaces;

class Scope implements Interfaces\TemplateCompilerScope
{
    protected $compiled = null;
    protected $php = null;
    protected $name = null;

    /**
     * @var array|null
     */
    protected $data = null;
    
    public function setName($name)
    {
        $this->name = $name;
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
    
    public function getName()
    {
        return $this->name;
    }
}