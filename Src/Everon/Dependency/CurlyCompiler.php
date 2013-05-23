<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Dependency;


trait CurlyCompiler
{

    protected $CurlyCompiler = null;


    /**
     * @return \Everon\Interfaces\TemplateCompiler
     */
    public function getCurlyCompiler()
    {
        return $this->CurlyCompiler;
    }

    /**
     * @param \Everon\Interfaces\TemplateCompiler $CurlyCompiler
     */
    public function setCurlyCompiler(\Everon\Interfaces\TemplateCompiler $CurlyCompiler)
    {
        $this->CurlyCompiler = $CurlyCompiler;
    }

}
