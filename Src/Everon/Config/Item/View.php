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

class View extends \Everon\Config\Item implements Interfaces\ConfigItem, Interfaces\Arrayable
{
    use Helper\Asserts;
    use Helper\Asserts\IsStringAndNonEmpty;
    use Helper\Regex;
    use Helper\ToArray;

    protected $title = null;
    
    protected $lang = null;
    
    protected $static_url = null;
    
    protected $charset = null;
    
    protected $keywords = null;
    
    protected $description = null;
    
    protected $body = null;
    
    protected $routes = [];
    

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
            'items' => [],
            'default' => false,
        ];

        $this->data = array_merge($empty_defaults, $data);
        $this->validateData($this->data);

        $this->setName($this->data['____name']);
        $this->setIsDefault($this->data['default']);
    }

    /**
     * @param array $data
     */
    public function validateData(array $data)
    {
        $this->assertIsStringAndNonEmpty($data['____name'], 'Invalid view name: "%s"', 'ConfigItemView');
    }

}