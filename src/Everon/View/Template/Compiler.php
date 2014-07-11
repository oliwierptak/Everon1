<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\View\Template;

use Everon\Dependency;
use Everon\Helper;
use Everon\View\Interfaces;


abstract class Compiler implements Interfaces\TemplateCompiler
{
    use Dependency\Injection\Logger;

    use Helper\Arrays;
    use Helper\IsIterable;


    /**
     * @inheritdoc
     */    
    abstract public function compile(Interfaces\TemplateCompilerContext $Context);
}