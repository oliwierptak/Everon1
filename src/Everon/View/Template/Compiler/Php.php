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
    public function compile($scope_name, $template_content, array $data)
    {
        $Scope = new Scope();
        $Scope->setName('View');
        $Scope->setPhp($template_content);

        try {
            $this->scope_name = $scope_name;
            $ScopeData = new Helper\PopoProps($data);
            $code = $this->runPhp($template_content, ['Tpl' => $ScopeData], $this->getFileSystem());
            $Scope->setCompiled($code);
            $Scope->setData($data);
            
            $this->getLogger()->php($code);

            return $Scope;
        }
        catch (\Exception $e) {
            $this->getLogger()->error($e);
            return $Scope;
        }
    }
}