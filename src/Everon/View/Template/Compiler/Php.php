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

use Everon\Dependency;
use Everon\Helper;
use Everon\View\Interfaces;
use Everon\View\Template\Compiler;

class Php extends Compiler
{
    use Dependency\Injection\FileSystem;
    
    use Helper\String\Compiler;
    use Helper\String\EndsWith;
    use Helper\RunPhp;

    /**
     * @inheritdoc
     */
    public function compile(Interfaces\TemplateCompilerContext $Context)
    {
        try {
            $this->runPhp($Context, $this->getFileSystem());
        }
        catch (\Exception $e) {
            $this->getLogger()->view($e."\n".$Context->getPhp());
            $Context->setCompiled($e->getMessage());
            //$content = 'Template error: '.$e->getMessage().' on line '.$e->getLine().' in '.$scope;
        }
    }
}