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
use Everon\Helper;
use Everon\Interfaces;

abstract class Manager implements Interfaces\ViewManager
{
    use Dependency\Injection\Factory;
    use Helper\String\LastTokenToName;

    protected $views = [];

    protected $compilers = [];

    protected $view_template_directory = null;

    protected $view_cache_directory = null;


    abstract protected function compileTemplate(Interfaces\TemplateContainer $Template);


    public function __construct(array $compilers, $template_directory, $cache_directory)
    {
        $this->compilers = $compilers;
        $this->view_template_directory = $template_directory;
        $this->view_cache_directory = $cache_directory;
    }

    public function getCompilers()
    {
        return $this->compilers;
    }

    /**
     * @param $name
     * @return mixed
     * @throws Exception\ViewManager
     */
    public function getView($name)
    {
        if (isset($this->views[$name]) === false) {
            $template_directory = $this->view_template_directory.$name.DIRECTORY_SEPARATOR;
            if  ((new \SplFileInfo($template_directory))->isDir() === false) {
                throw new Exception\ViewManager('View template directory: "%s" does not exist', $template_directory);
            }

            $this->views[$name] = $this->getFactory()->buildView($name, $template_directory);
        }

        return $this->views[$name];
    }

    /**
     * @param $name
     * @param Interfaces\View $View
     */
    public function setView($name, Interfaces\View $View)
    {
        $this->views[$name] = $View;
    }

}