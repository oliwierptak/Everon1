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

class View extends \Everon\Config\Item implements Interfaces\ConfigItem
{
    use Helper\Asserts;
    use Helper\Asserts\IsStringAndNonEmpty;
    use Helper\Regex;

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
        parent::__construct($data, [
            'title' => '',
            'lang' => 'en-GB',
            'static_url' => 'static/',
            'charset' => 'UTF-8',
            'keywords' => '',
            'description' => '',
            'body' => '',
            'items' => [],
        ]);
    }
    
    protected function init()
    {
        parent::init();
    }

    /**
     * @param array $data
     * @throws Exception\ConfigItem
     */
    public function validateData(array $data)
    {
        parent::validateData($data);
        $this->assertIsStringAndNonEmpty($data['title'], 'Invalid title: "%s"', 'ConfigItem');
    }

}