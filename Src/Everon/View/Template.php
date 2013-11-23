<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\View;

use Everon\Exception;
use Everon\Interfaces;

class Template extends Template\Container implements Interfaces\Template
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
        $this->template_file = $filename;

        $LoadOnDemand = function() {
            $this->loadTemplate();
        };

        parent::__construct($LoadOnDemand, $data);
    }

    protected function loadTemplate()
    {
        $this->validateTemplateFilename();
        $this->setTemplateContent(file_get_contents($this->getTemplateFile()));
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
     * @inheritdoc
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

}