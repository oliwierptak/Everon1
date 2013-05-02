<?php
namespace Everon\View\Template;

use Everon\Helper;
use Everon\Interfaces;


class Container implements Interfaces\TemplateContainer, Interfaces\Arrayable
{
    use Helper\Asserts;
    use Helper\Asserts\IsArrayKey;
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

    /**
     * @var array
     */
    protected $includes = [];


    /**
     * @param string|\Closure $content
     * @param array $data
     */
    public function __construct($content, array $data)
    {
        $this->setData($data);
        $this->setTemplateContent($content);
    }

    /**
     * @param $name
     * @param $value
     * @return Interfaces\TemplateCompiler
     */
    public function set($name, $value)
    {
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
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->data = $data;
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
     * @param $name
     * @param Interfaces\TemplateContainer $Include
     * @return Interfaces\TemplateContainer
     */
    public function setInclude($name, Interfaces\TemplateContainer $Include)
    {
        $this->includes[$name] = $Include;
        return $this;
    }

    /**
     * @param $name
     * @return Interfaces\TemplateContainer
     */
    public function getInclude($name)
    {
        $this->assertIsArrayKey($name, $this->includes, 'Invalid included template name: %s', 'Template');
        return $this->includes[$name];
    }

    public function setAllIncludes(array $includes)
    {
        $this->includes = $includes;
    }

    public function getAllIncludes()
    {
        return $this->includes;
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