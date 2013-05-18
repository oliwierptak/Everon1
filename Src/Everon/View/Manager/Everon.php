<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\View\Manager;

use Everon\Interfaces;
use Everon\Exception;
use Everon\View;

class Everon extends View\Manager implements Interfaces\ViewManager
{

    /**
     * @param Interfaces\TemplateContainer $Template
     * @throws Exception|\Exception
     * @throws Exception\TemplateCompiler
     */
    protected function compileTemplate(Interfaces\TemplateContainer $Template)
    {
        try {
            $compiled_content = null;
            
            /**
             * @var Interfaces\TemplateCompiler $Compiler
             * @var Interfaces\TemplateContainer $Include
             * @var Interfaces\TemplateContainer $TemplateInclude
             */
            foreach ($this->compilers as $Compiler) {
                foreach ($Template->getData() as $name => $Include) {
                    if (($Include instanceof Interfaces\TemplateContainer) === false) {
                        continue;
                    }
                    
                    foreach ($Include->getData() as $include_name => $TemplateInclude) {
                        if (($TemplateInclude instanceof Interfaces\TemplateContainer) === false) {
                            continue;
                        }
                        
                        $this->compileTemplate($TemplateInclude);
                        $Include->set($include_name, $TemplateInclude->getCompiledContent());
                    }

                    $Include->setCompiledContent(
                        $Compiler->compile($Include->getTemplateContent(), $Include->getData())
                    );
                    $Template->set($name, $Include->getCompiledContent());
                }

                $compiled_content = $compiled_content ?: $Template->getTemplateContent();
                $compiled_content = $Compiler->compile($compiled_content, $Template->getData());
            }
            $Template->setCompiledContent($compiled_content);
        }
        catch (Exception $e) {
            throw $e;
        }
        catch (\Exception $e) {
            throw new Exception\TemplateCompiler($e);
        }
    }    

}
