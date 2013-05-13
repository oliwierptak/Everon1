<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Config\Item;

use Everon\Exception;
use Everon\Interfaces;
use Everon\Helper;

class View implements Interfaces\ConfigItemView, Interfaces\Arrayable
{
    use Helper\Asserts;
    use Helper\Asserts\IsStringAndNonEmpty;
    use Helper\Regex;
    use Helper\ToArray;

    protected $view_name = null;

    protected $title = null;
    
    protected $lang = null;
    
    protected $static_url = null;
    
    protected $charset = null;
    
    protected $keywords = null;
    
    protected $description = null;
    
    protected $body = null;
    
    protected $routes = [];
    

    /**
     * @var boolean
     */
    protected $is_default = null;


    public function __construct(array $data)
    {
        $this->init($data);
    }

    protected function init(array $data)
    {
        $empty_defaults = [
            'title' => '',
            'lang' => 'en-GB',
            'static_url' => 'static/',
            'charset' => 'UTF-8',
            'keywords' => '',
            'description' => '',
            'body' => '',
            'routes' => [],
            'view_name' => null,
            'default' => false,
        ];

        $this->data = array_merge($empty_defaults, $data);
        $this->validateData($this->data);

        $this->setName($this->data['view_name']);
        $this->setIsDefault($this->data['default']);
    }

    /**
     * @param array $data
     */
    public function validateData(array $data)
    {
        $this->assertIsStringAndNonEmpty($data['view_name'], 'Invalid view name: "%s"', 'ConfigItemView');
    }

    public function getName()
    {
        return $this->view_name;
    }

    /**
     * @param $name
     */
    public function setName($name)
    {
        $this->view_name = $name;
    }

    /**
     * @return bool
     */
    public function isDefault()
    {
        return $this->is_default;
    }

    /**
     * @param boolean $is_default
     */
    public function setIsDefault($is_default)
    {
        $this->is_default = (bool) $is_default;
    }
    
}