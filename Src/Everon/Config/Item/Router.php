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

class Router extends \Everon\Config\Item implements Interfaces\ConfigItemRouter
{
    use Helper\Asserts;
    use Helper\Asserts\IsStringAndNonEmpty;
    use Helper\Regex;

    protected $url = null;

    protected $controller = null;

    protected $action = null;

    /**
     * @var array
     */
    protected $regex_get = [];

    /**
     * @var array
     */
    protected $regex_post = [];
    
    
    public function __construct(array $data)
    {
        parent::__construct($data, [
            'url' => null,
            'controller' => null,
            'action' => null,
            'get' => [],
            'post' => []
        ]);
    }

    protected function init()
    {
        parent::init();
        $this->setUrl($this->data['url']);
        $this->setController($this->data['controller']);
        $this->setAction($this->data['action']);
        $this->setGetRegex($this->data['get']);
        $this->setPostRegex($this->data['post']);
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
        $str = current($query_tokens);

        return $str;
    }

    /**
     * @param $get_data
     * @return mixed
     */
    public function filterQueryKeys($get_data)
    {
        $keys_to_keep = array_keys($get_data);
        return $this->filterKeys($keys_to_keep, $this->getGetRegex(), true);
    }

    /**
     * @param $get_data
     * @return mixed
     */
    public function filterGetKeys($get_data)
    {
        $keys_to_remove = array_keys($get_data);
        return $this->filterKeys($keys_to_remove, $this->getGetRegex(), false);
    }

    /**
     * @param $request_url
     * @return bool
     */
    public function matchesByUrl($request_url)
    {
        try {
            $data = $this->getGetRegex();
            $pattern = $this->getCleanUrl($this->getUrl());
            
            if (is_array($data)) {
                $pattern = $this->replaceCurlyParametersWithRegex($pattern, $data);
            }

            $pattern = $this->regexCompleteAndValidate($this->getName(), $pattern);
            $subject = $this->getCleanUrl($request_url);

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
        $this->assertIsStringAndNonEmpty($data['controller'], 'Invalid controller: "%s"', 'ConfigItem');
        $this->assertIsStringAndNonEmpty($data['action'], 'Invalid action: "%s"', 'ConfigItem');
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
    }

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
    
}