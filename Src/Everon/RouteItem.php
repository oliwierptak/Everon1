<?php
namespace Everon;

class RouteItem implements Interfaces\RouteItem, Interfaces\Arrayable
{
    use Helper\Asserts;
    use Helper\Asserts\IsStringAndNonEmpty;
    use Helper\Regex;
    use Helper\ToArray;

    protected $route_name = null;

    protected $url = null;

    protected $controller = null;

    protected $action = null;

    /**
     * @var boolean
     */
    protected $is_default = null;

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
        $this->init($data);
    }

    protected function init(array $data)
    {
        $empty_defaults = [
            'route_name' => null,
            'url' => null,
            'controller' => null,
            'action' => null,
            'get' => [],
            'post' => [],
            'default' => false,
        ];

        $this->data = array_merge($empty_defaults, $data);
        $this->validateData($this->data);

        $this->setName($this->data['route_name']);
        $this->setUrl($this->data['url']);
        $this->setController($this->data['controller']);
        $this->setAction($this->data['action']);
        $this->setGetRegex($this->data['get']);
        $this->setPostRegex($this->data['post']);
        $this->setIsDefault($this->data['default']);
    }

    /**
     * Removes everything after ? (eg. ?param1=1&param2=2)
     *
     * @param $str
     * @param string $marker
     * @return mixed
     */
    protected function getCleanUrl($str, $marker='?')
    {
        $query_tokens = explode($marker, $str);
        $str = current($query_tokens);

        return $str;
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
    protected function replaceCurlyParametersWithRegex($pattern, array $data)
    {
        foreach ($data as $name => $regex) {
            $pattern = str_replace('{'.$name.'}', '('.$regex.')', $pattern);
        }

        return $pattern;
    }

    /**
     * @param $get_data
     * @return mixed
     */
    protected function filterQueryKeys($get_data)
    {
        $keys_to_keep = array_keys($get_data);
        return $this->filterKeys($keys_to_keep, $this->getGetRegex(), true);
    }

    /**
     * @param $get_data
     * @return mixed
     */
    protected function filterGetKeys($get_data)
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
     * Matches urls like /news/show/12 with /news/show/{id} and returns ['id' => 12]
     * Also checks if $_GET values are set according to the regex in router.ini
     *
     * Returns merged data from parsed query string and _GET
     *
     * @param $request_url
     * @param $get_data
     * @return array|null
     * @throws Exception\RouteItem
     */
    public function validateQueryAndGet($request_url, array $get_data)
    {
        try {
            $parsed_query = $this->validateQuery($request_url, $get_data);
            $parsed_get = $this->validateGet($get_data);
            return array_merge($parsed_query, $parsed_get);
        }
        catch (\Exception $e) {
            throw new Exception\RouteItem($e);
        }
    }

    /**
     * @param $request_url
     * @param array $get_data
     * @return array
     */
    protected function validateQuery($request_url, array $get_data)
    {
        $request_url = $this->getCleanUrl($request_url);
        $regex_url = $this->getCleanUrl($this->getUrl());

        $parsed_query = [];
        $validators_for_query = $this->filterQueryKeys($get_data);
        if (is_array($validators_for_query)) {
            $url_pattern = $this->replaceCurlyParametersWithRegex($regex_url, $validators_for_query);
            $url_pattern = $this->regexCompleteAndValidate($this->getName(), $url_pattern);

            if (preg_match($url_pattern, $request_url, $params_tokens)) {
                array_shift($params_tokens); //remove url
                if (count($validators_for_query) == count($params_tokens)) {
                    $parsed_query = array_combine(array_keys($validators_for_query), array_values($params_tokens));
                }
            }
        }

        return $parsed_query;
    }

    /**
     * @param array $get_data
     * @return array
     */
    protected function validateGet(array $get_data)
    {
        $parsed_get = [];
        $validators_for_get = $this->filterGetKeys($get_data);
        if (is_array($validators_for_get)) {
            foreach ($validators_for_get as $regex_name => $regex) {
                $subject = $get_data[$regex_name];
                $pattern = $this->regexCompleteAndValidate($this->getName(), $regex);
                if (preg_match($pattern, $subject) === 1) {
                    $parsed_get[$regex_name] = $get_data[$regex_name];
                }
            }
        }

        return $parsed_get;
    }    

    /**
     * @param array $post_data
     * @return array
     * @throws Exception\RouteItem
     */
    public function validatePost(array $post_data)
    {
        try {
            foreach ($post_data as $param_name => $pvalue) {
                foreach ($this->regex_post as $regex_name => $regex) {
                    if (strcasecmp($param_name, $regex_name) !== 0) {
                        continue;
                    }

                    $subject = $post_data[$param_name];
                    $pattern = $this->regexCompleteAndValidate($this->getName(), $regex);
                    if (preg_match($pattern, $subject, $params_tokens) === 0) {
                        unset($post_data[$param_name]);  //remove invalid post
                    }
                }
            }

            return $post_data;
        }
        catch (\Exception $e) {
            throw new Exception\RouteItem($e);
        }
    }

    /**
     * @param array $data
     */
    public function validateData(array $data)
    {
        $this->assertIsStringAndNonEmpty($data['route_name'], 'Invalid route name: "%s"', 'RouteItem');
        $this->assertIsStringAndNonEmpty($data['url'], 'Invalid url: "%s"', 'RouteItem');
        $this->assertIsStringAndNonEmpty($data['controller'], 'Invalid controller: "%s"', 'RouteItem');
        $this->assertIsStringAndNonEmpty($data['action'], 'Invalid action: "%s"', 'RouteItem');
    }    

    public function getName()
    {
        return $this->route_name;
    }

    /**
     * @param $route_name
     */
    public function setName($route_name)
    {
        $this->route_name = $route_name;
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