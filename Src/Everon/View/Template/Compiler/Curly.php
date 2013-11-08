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

use Everon\Helper;
use Everon\Interfaces;

class Curly implements Interfaces\TemplateCompiler
{
    use Helper\IsIterable;
    use Helper\String\Compiler;

    /**
     * @param $template_content
     * @param array $data
     * @return string
     */
    public function compile($template_content, array $data)
    {
        return $this->stringCompilerCompile($template_content, $data, ['{','}']);
    }

}