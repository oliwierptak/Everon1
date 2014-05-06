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

use Everon\Exception;
use Everon\Helper;
use Everon\View\Interfaces;


class Container implements Interfaces\TemplateContainer
{
    use Helper\Arrays;
    use Helper\ToString;
    use Helper\ToArray;
    
    /**
     * @var array|null
     */
    protected $compiled_content = null;

    /**
     * @var string|\Closure
     */
    protected $template_content = null;
    
    protected $Scope = null;


    /**
     * @param string|\Closure $content
     * @param array $data
     */
    public function __construct($content, array $data)
    {
        $this->template_content = $content;
        $this->data = $data;
    }
    
    protected function resetCompiledContent()
    {
        $this->setCompiledContent(null);
    }

    /**
     * @inheritdoc
     */
    public function set($name, $value)
    {
        if (isset($this->data[$name]) && $this->data[$name] !== $value) {
            $this->resetCompiledContent();
        }
        
        $this->data[$name] = $value;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function get($name, $default=null)
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        return $default;
    }

    /**
     * @inheritdoc
     */
    public function delete($name)
    {
        $this->data[$name] = null;
        unset($this->data[$name]);
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @inheritdoc
     */
    public function setData(array $data)
    {
        $this->data = $data;
        $this->resetCompiledContent();
    }

    /**
     * @inheritdoc
     */
    public function getCompiledContent()
    {
        return $this->compiled_content;
    }

    /**
     * @inheritdoc
     */
    public function setCompiledContent($content)
    {
        $this->compiled_content = $content;
    }

    /**
     * @inheritdoc
     */
    public function getTemplateContent()
    {
        if ($this->template_content instanceof \Closure) {
            $this->template_content->__invoke();
        }

        return $this->template_content;
    }

    /**
     * @inheritdoc
     */
    public function setTemplateContent($content)
    {
        $this->template_content = $content;
    }

    /**
     * @inheritdoc
     */
    public function setScope(Interfaces\TemplateCompilerScope $Scope)
    {
        $this->Scope = $Scope;
    }

    /**
     * @inheritdoc
     */
    public function getScope()
    {
        return $this->Scope;
    }    
    
    /**
     * @return string
     */
    protected function getToString()
    {
        return (string) $this->getCompiledContent();
    }
    
}