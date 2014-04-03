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
     * @param $php
     * @param array $scope
     * @param \Everon\Interfaces\FileSystem $FileSystem
     * @return string
     */
    protected function runPhp($php, array $scope, \Everon\Interfaces\FileSystem $FileSystem)
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
        $content = '';
        try {
            $TmpPhpFile->write($php);
            $php_file = $TmpPhpFile->getFilename(); 

            ob_start();
            extract($scope);
            
            $View = $Tpl->View;

            unset($Tpl->View);
            
            include $php_file;
            $content = ob_get_contents();
            
        }
        catch (\Exception $e) {
            $this->getLogger()->e_error($e."\n".$php);
            $content = $e->getMessage().' on line '.$e->getLine();
        }
        finally {
            ob_end_clean();
            $TmpPhpFile->close();
            restore_error_handler($old_error_handler);
            return $content;
        }
    }
}
