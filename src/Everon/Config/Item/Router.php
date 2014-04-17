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

use Everon\Config;
use Everon\Exception;
use Everon\Interfaces;
use Everon\Helper;

class Router extends Config\Item implements Config\Interfaces\ItemRouter
{
    const PROPERTY_MODULE = 'module';
    
    use Helper\Arrays;
    use Helper\Exceptions;
    use Helper\Asserts\IsStringAndNonEmpty;
    use Helper\IsIterable;
    use Helper\Regex;

    protected $url = null;
    
    protected $module = null;

    protected $controller = null;

    protected $action = null;

    protected $regex_get = [];
    
    protected $regex_query = [];

    protected $regex_post = [];
    
    protected $method = null;

    
    public function __construct(array $data)
    {
        parent::__construct($data, [
            'url' => null,
            'controller' => null,
            'action' => null,
            'method' => null,
            'get' => [],
            'query' => [],
            'post' => []
        ]);
    }

    protected function init()
    {
        parent::init();
        $this->setModule($this->data[static::PROPERTY_MODULE]);
        unset($this->data[static::PROPERTY_MODULE]);
        
        $this->setUrl($this->data['url']);
        $this->setController($this->data['controller']);
        $this->setAction($this->data['action']);
        $this->setGetRegex($this->data['get']);
        $this->setQueryRegex($this->data['query']);
        $this->setPostRegex($this->data['post']);
        $this->setMethod($this->data['method']);
    }

    /**
     * @param $keys
     * @param $data
     * @param $keep
     * @return mixed
     */
    protected function filterKeys($keys, $data, $keep)
    {
        foreach ($data as $name => $value) {
            if (in_array($name, $keys) === $keep) {
                unset($data[$name]);
            }
        }

        return $data;
    }

    /**
     * Takes login/submit/session/{sid}/redirect/{location}?and=something
     * and returns @^login/submit/session/([a-z0-9]+)/redirect/([a-zA-Z0-9|%]+)$@
     * according to router.ini
     *
     * @param $pattern
     * @param array $data
     * @return string
     */
    public function replaceCurlyParametersWithRegex($pattern, array $data)
    {
        foreach ($data as $name => $regex) {
            $pattern = str_replace('{'.$name.'}', '('.$regex.')', $pattern);
        }

        return $pattern;
    }

    /**
     * Removes everything after ? (eg. ?param1=1&param2=2)
     *
     * @param $str
     * @param string $marker
     * @return mixed
     */
    public function getCleanUrl($str, $marker='?')
    {
        $query_tokens = explode($marker, $str);
        return current($query_tokens);
    }

    /**
     * @param $get_data
     * @return mixed
     */
    public function filterQueryKeys($get_data)
    {
        $keys_to_keep = array_keys($get_data);
        return $this->arrayKeep($keys_to_keep, $this->getQueryRegex());
    }

    /**
     * @param $get_data
     * @return mixed
     */
    public function filterGetKeys($get_data)
    {
        $keys_to_remove = array_keys($get_data);
        return $this->arrayRemove($keys_to_remove, $this->getGetRegex());
    }

    /**
     * @param $request_path
     * @return bool
     */
    public function matchesByPath($request_path)
    {
        try {
            $data = $this->getQueryRegex();
            $pattern = $this->getCleanUrl($this->getUrl());
            
            if (is_array($data)) {
                $pattern = $this->replaceCurlyParametersWithRegex($pattern, $data);
            }

            $pattern = $this->regexCompleteAndValidate($this->getName(), $pattern);
            $subject = $this->getCleanUrl($request_path);

            return preg_match($pattern, $subject, $matches) === 1;
        }
        catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param array $data
     * @throws Exception\ConfigItem
     */
    public function validateData(array $data)
    {
        parent::validateData($data);
        $this->assertIsStringAndNonEmpty($data['url'], 'Invalid url: "%s"', 'ConfigItem');
        $this->assertIsStringAndNonEmpty($data[static::PROPERTY_MODULE], 'Invalid item module name: "%s"', 'ConfigItem');
        $this->assertIsStringAndNonEmpty($data['controller'], 'Invalid controller: "%s"', 'ConfigItem');
        $this->assertIsStringAndNonEmpty($data['action'], 'Invalid action: "%s"', 'ConfigItem');
    }

    /**
     * @inheritdoc
     */
    public function setModule($module)
    {
        $this->module = $module;
    }

    /**
     * @inheritdoc
     */
    public function getModule()
    {
        return $this->module;
    }

    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
        $this->data['url'] = $url;
    }

    public function getController()
    {
        return $this->controller;
    }

    /**
     * @param $controller
     */
    public function setController($controller)
    {
        $this->controller = $controller;
        $this->data['controller'] = $controller;
    }

    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param $action
     */
    public function setAction($action)
    {
        $this->action = $action;
        $this->data['action'] = $action;
    }

    /**
     * @return array
     */    
    public function getGetRegex()
    {
        return $this->regex_get;
    }

    /**
     * @param $regex
     */   
    public function setGetRegex($regex)
    {
        $this->regex_get = $regex;
    }

    /**
     * @return array
     */
    public function getQueryRegex()
    {
        return $this->regex_query;
    }

    /**
     * @param $regex
     */
    public function setQueryRegex($regex)
    {
        $this->regex_query = $regex;
    }

    /**
     * @return array
     */
    public function getPostRegex()
    {
        return $this->regex_post;
    }

    /**
     * @param $regex
     */
    public function setPostRegex($regex)
    {
        $this->regex_post = $regex;
    }
    
    /**
     * @inheritdoc
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @inheritdoc
     */
    public function getMethod()
    {
        return $this->method;
    }   
}