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

use Everon\Exception;
use Everon\Helper;
use Everon\Interfaces;
use Everon\Dependency;

class CurlyNoEval extends Curly
{
    protected function evalPhp($matches) 
    {
        try {
            $code = $this->phpize($matches[1]);
            $code = rtrim($code, ';').";";
            $output = "<?php $code ?>";
            $this->getLogger()->curly_no_eval($output);
            return $output;
        }
        catch (\Exception $e) {
            $output = false;
        }
        return '';
    }

    protected function stringCompilerCompile22($template_content, array $data)
    {
        return $template_content;
    }
}