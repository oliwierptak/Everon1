<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Interfaces;

use Everon\Exception;
use Everon\Interfaces;

interface ViewManager
{
    /**
     * @param Interfaces\Template $Template
     * @throws Exception|\Exception
     * @throws Exception\TemplateCompiler
     */
    function compileTemplate(Interfaces\Template $Template);
        
    /**
     * @param $name
     * @return mixed
     */
    function getView($name);

    /**
     * @param $name
     * @param Interfaces\View $View
     */
    public function setView($name, Interfaces\View $View);

    /**
     * @return array
     */
    function getCompilers();

    /**
     * @return Interfaces\View
     */
    function getDefaultView();
}