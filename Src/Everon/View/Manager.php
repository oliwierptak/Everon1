<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\View;

use Everon\Dependency;
use Everon\Exception;
use Everon\Interfaces;

abstract class Manager implements Interfaces\ViewManager
{
    use Dependency\Injection\Factory;

    /**
     * @var array
     */
    protected $views = null;

    protected $compilers = [];

    protected $view_template_directory = null;


    abstract protected function compileTemplate(Interfaces\TemplateContainer $Template);


    public function __construct(array $compilers, $view_template_directory)
    {
        $this->compilers = $compilers;
        $this->view_template_directory = $view_template_directory;
    }

    /**
     * @param array $compilers
     */
    public function setCompilers(array $compilers)
    {
        $this->compilers = $compilers;
    }

    public function getCompilers()
    {
        return $this->compilers;
    }
    
    /**
     * @param $name
     * @return mixed
     */
    public function getView($name)
    {
        if (isset($this->views[$name]) === false) {
            $tokens = explode('\\', $name);
            $name = array_pop($tokens);
            $view_template_directory = $this->view_template_directory.$name.DIRECTORY_SEPARATOR;
            $this->views[$name] = $this->getFactory()->buildView($name, $view_template_directory, function(Interfaces\TemplateContainer $Template) {
                $this->compileTemplate($Template);
            });
        }

        return $this->views[$name];
    }    

}