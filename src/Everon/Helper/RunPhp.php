<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Helper;

trait RunPhp
{
    /**
     * Must implement Logger
     * 
     * @param \Everon\View\Interfaces\TemplateCompilerContext $Context
     * @param \Everon\Interfaces\FileSystem $FileSystem
     */
    protected function runPhp(\Everon\View\Interfaces\TemplateCompilerContext $Context, \Everon\Interfaces\FileSystem $FileSystem)
    {
        $handleError = function($errno, $errstr, $errfile, $errline, array $errcontext) {
            // error was suppressed with the @-operator
            if (0 === error_reporting()) {
                return false;
            }

            throw new \Everon\Exception\TemplateEvaluationError($errstr, 0, $errno, $errfile, $errline);
        };

        $old_error_handler = set_error_handler($handleError);

        $TmpPhpFile = $FileSystem->createTmpFile();
        try {
            $TmpPhpFile->write($Context->getPhp());
            $php_file = $TmpPhpFile->getFilename();
            
            ob_start();


            $callback = function() use ($php_file, $Context) {
                extract([
                    'Tpl' => new PopoProps($Context->getData()),
                ]);
                include $php_file;
            };
            
            $callback = $callback->bindTo($Context->getScope() ?: $this);
            $callback();
            
            $Context->setCompiled(ob_get_contents());
        }
        finally {
            ob_end_clean();
            $TmpPhpFile->close();
            restore_error_handler($old_error_handler);
        }
    }
}
