<?php
namespace Everon\View;

use Everon\Exception;
use Everon\Interfaces;

class Template extends Template\Container implements Interfaces\TemplateContainer
{

    /**
     * @var string
     */
    protected $template_file = null;


    /**
     * @param $filename
     * @param array $data
     */
    public function __construct($filename, array $data)
    {
        $this->setTemplateFile($filename);

        $LoadOnDemand = function() {
            $this->loadTemplate();
        };

        parent::__construct($LoadOnDemand, $data);
    }

    /**
     * @throws Exception\Template
     */
    protected function validateTemplateFilename()
    {
        if (!is_file($this->getTemplateFile())) {
            throw new Exception\Template('Template file: "%s" was not found', $this->getTemplateFile());
        }
    }

    /**
     * @return string
     */
    public function getTemplateFile()
    {
        return $this->template_file;
    }

    /**
     * @param $filename
     */
    public function setTemplateFile($filename)
    {
        $this->template_file = $filename;
    }

    protected function loadTemplate()
    {
        $this->validateTemplateFilename();
        $this->setTemplateContent(file_get_contents($this->getTemplateFile()));
    }
}