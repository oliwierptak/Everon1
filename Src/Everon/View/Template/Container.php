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
use Everon\Interfaces;


class Container implements Interfaces\TemplateContainer, Interfaces\Arrayable
{
    use Helper\Asserts;
    use Helper\Asserts\IsArrayKey;
    use Helper\ToString;
    use Helper\ToArray;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var array|null
     */
    protected $compiled_content = null;

    /**
     * @var string|\Closure
     */
    protected $template_content = null;


    /**
     * @param string|\Closure $content
     * @param array $data
     */
    public function __construct($content, array $data)
    {
        $this->data = $data;
        $this->template_content = $content;
    }

    /**
     * @param $name
     * @param $value
     * @return Interfaces\TemplateCompiler
     */
    public function set($name, $value)
    {
        if ($this->compiled_content !== null && isset($this->data[$name])) {
            throw new Exception\Template('Setting existing property: "%s"');
        }
        
        $this->data[$name] = $value;
        return $this;
    }

    /**
     * @param $name
     * @param null $default
     * @return mixed|null
     */
    public function get($name, $default=null)
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        return $default;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getCompiledContent()
    {
        return $this->compiled_content;
    }

    /**
     * @param string $content
     */
    public function setCompiledContent($content)
    {
        $this->compiled_content = $content;
    }

    /**
     * @return string
     */
    public function getTemplateContent()
    {
        if ($this->template_content instanceof \Closure) {
            $this->template_content->__invoke();
        }

        return $this->template_content;
    }

    /**
     * @param string $content
     */
    public function setTemplateContent($content)
    {
        $this->template_content = $content;
    }

    /**
     * @return string
     */
    public function getToString()
    {
        if ($content = $this->getCompiledContent()) {
            return $content;
        }

        return (string) $this->getTemplateContent();
    }

}