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
    const PROPERTY_MODULE = '____module';
    
    use Helper\Arrays;
    use Helper\Asserts\IsStringAndNotEmpty;
    use Helper\Exceptions;
    use Helper\IsIterable;
    use Helper\Regex;
    use Helper\String\Compiler;

    protected $url = null;
    
    protected $module = null;

    protected $controller = null;

    protected $action = null;

    protected $regex_get = [];
    
    protected $regex_query = [];

    protected $regex_post = [];
    
    protected $method = null;
    
    protected $parsed_url = null;

    /**
     * @var boolean
     */
    protected $secure = false;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $section_name = @$data[static::PROPERTY_NAME];
        $tokens = explode('@', $section_name);
        
        if (count($tokens) === 2) {
            $data[static::PROPERTY_MODULE] = trim($tokens[0]);
        }

        parent::__construct($data, [
            'url' => null,
            'controller' => null,
            'action' => null,
            'method' => 'GET',
            'get' => [],
            'query' => [],
            'post' => [],
            'secure' => false
        ]);
    }

    protected function init()
    {
        parent::init();
        $this->setModule($this->data[static::PROPERTY_MODULE]);
        unset($this->data[static::PROPERTY_MODULE]);
        
        $this->setUrl($this->data['url']);
        $this->setParsedUrl($this->data['url']);
        $this->setController($this->data['controller']);
        $this->setAction($this->data['action']);
        $this->setGetRegex($this->data['get']);
        $this->setQueryRegex($this->data['query']);
        $this->setPostRegex($this->data['post']);
        $this->setMethod($this->data['method']);
        $this->setIsSecure($this->data['secure']);
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
     * @inheritdoc
     */
    public function replaceCurlyParametersWithRegex($pattern, array $data)
    {
        foreach ($data as $name => $regex) {
            $pattern = str_replace('{'.$name.'}', '('.$regex.')', $pattern);
        }

        return $pattern;
    }

    /**
     * @inheritdoc
     */
    public function getCleanUrl($str, $marker='?')
    {
        $query_tokens = explode($marker, $str);
        return current($query_tokens);
    }

    /**
     * @inheritdoc
     */
    public function filterQueryKeys($get_data)
    {
        $keys_to_keep = array_keys($get_data);
        return $this->arrayKeep($keys_to_keep, $this->getQueryRegex());
    }

    /**
     * @inheritdoc
     */
    public function filterGetKeys($get_data)
    {
        $keys_to_remove = array_keys($get_data);
        return $this->arrayRemove($keys_to_remove, $this->getGetRegex());
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    public function validateData(array $data)
    {
        parent::validateData($data);
        $this->assertIsStringAndNonEmpty((string) @$data['url'], 'Invalid url: "%s"', 'ConfigItem');
        $this->assertIsStringAndNonEmpty((string) @$data[static::PROPERTY_MODULE], 'Invalid item module name: "%s"', 'ConfigItem');
        $this->assertIsStringAndNonEmpty((string) @$data['controller'], 'Invalid controller: "%s"', 'ConfigItem');
        $this->assertIsStringAndNonEmpty((string) @$data['action'], 'Invalid action: "%s"', 'ConfigItem');
        $this->assertIsStringAndNonEmpty((string) @$data['method'], 'Invalid method: "%s"', 'ConfigItem');
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

    /**
     * @inheritdoc
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @inheritdoc
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @inheritdoc
     */
    public function getController()
    {
        return $this->controller;
    }
    
    /**
     * @inheritdoc
     */
    public function setController($controller)
    {
        $this->controller = $controller;
    }

    /**
     * @inheritdoc
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @inheritdoc
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * @inheritdoc
     */
    public function getGetRegex()
    {
        return $this->regex_get;
    }

    /**
     * @inheritdoc
     */ 
    public function setGetRegex($regex)
    {
        $this->regex_get = $regex;
    }

    /**
     * @inheritdoc
     */
    public function getQueryRegex()
    {
        return $this->regex_query;
    }

    /**
     * @inheritdoc
     */
    public function setQueryRegex($regex)
    {
        $this->regex_query = $regex;
    }

    /**
     * @inheritdoc
     */
    public function getPostRegex()
    {
        return $this->regex_post;
    }

    /**
     * @inheritdoc
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

    /**
     * @param boolean $secure
     */
    public function setIsSecure($secure)
    {
        $this->secure = $secure;
    }

    /**
     * @return boolean
     */
    public function isSecure()
    {
        return $this->secure;
    }
    
    /**
     * @inheritdoc
     */    
    public function getParsedUrl()
    {
        return $this->parsed_url;
    }

    /**
     * @inheritdoc
     */
    public function setParsedUrl($parsed_url)
    {
        $this->parsed_url = $parsed_url;
    }

    /**
     * @inheritdoc
     */
    public function compileUrl($parts)
    {
        $parsed_url = $this->stringCompilerRun($this->getUrl(), $parts);
        $this->setParsedUrl($parsed_url);
    }
}