<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon;

class RequestValidator implements Interfaces\RequestValidator
{
    use Helper\Exceptions;
    use Helper\Asserts\IsArrayKey;    
    use Helper\Regex;

    protected $errors = null;

    /**
     * Validates $_GET, $_POST and $QUERY_STRING.
     * Returns array of validated query, get and post, or throws an exception
     * 
     * @param Config\Interfaces\ItemRouter $RouteItem
     * @param Interfaces\Request $Request
     * @return array
     * @throws Exception\RequestValidator
     */
    public function validate(Config\Interfaces\ItemRouter $RouteItem, Interfaces\Request $Request)
    {
        $method = $RouteItem->getMethod();
        if ($method !== null && strcasecmp($method, $Request->getMethod()) !== 0) {
            throw new Exception\RequestValidator('Invalid request method: "%s", expected: "%s"', [$Request->getMethod(), $method]);
        }
        
        $this->errors = null;
        
        $parsed_query_parameters = $this->validateQuery($RouteItem, $Request->getPath(), $Request->getQueryCollection()->toArray());
        $this->validateRoute(
            $RouteItem->getName(),
            (array) $RouteItem->getQueryRegex(),
            $parsed_query_parameters,
            true //so 404 can be thrown
        );

        $parsed_get_parameters = $this->validateGet($RouteItem, $Request->getGetCollection()->toArray());
        $this->validateRoute(
            $RouteItem->getName(),
            (array) $RouteItem->getGetRegex(),
            $parsed_get_parameters,
            false
        );
        
        $parsed_post_parameters = $this->validatePost($RouteItem, $Request->getPostCollection()->toArray());
        $this->validateRoute(
            $RouteItem->getName(),
            (array) $RouteItem->getPostRegex(),
            $parsed_post_parameters,
            false
        );

        return [$parsed_query_parameters, $parsed_get_parameters, $parsed_post_parameters];
    }

    /**
     * @param $route_name
     * @param array $route_params
     * @param array $parsed_request_params
     * @param $throw
     * @throws Exception\InvalidRoute
     */
    protected function validateRoute($route_name, array $route_params, array $parsed_request_params, $throw)
    {
        foreach ($route_params as $name => $expression) {
            $msg = vsprintf('Invalid parameter: "%s" for route: "%s"', [$name, $route_name]);
            if (isset($parsed_request_params[$name]) === false) {
                $this->errors[$name] = $msg;
            }
            
            if ($throw) {
                $this->assertIsArrayKey($name, $parsed_request_params, $msg, 'InvalidRoute');
            }
        }
    }

    /**
     * @param Config\Interfaces\ItemRouter $RouteItem
     * @param $request_url
     * @param array $get_data
     * @return array
     * @throws Exception\RequestValidator
     */
    protected function validateQuery(Config\Interfaces\ItemRouter $RouteItem, $request_url, array $get_data)
    {
        try {
            $request_url = $RouteItem->getCleanUrl($request_url);
            $regex_url = $RouteItem->getCleanUrl($RouteItem->getUrl());
    
            $parsed_query = [];
            $validators_for_query = $RouteItem->filterQueryKeys($get_data);
            if (is_array($validators_for_query)) {
                $url_pattern = $RouteItem->replaceCurlyParametersWithRegex($regex_url, $validators_for_query);
                $url_pattern = $this->regexCompleteAndValidate($RouteItem->getName(), $url_pattern);
    
                if (preg_match($url_pattern, $request_url, $params_tokens)) {
                    array_shift($params_tokens); //remove url
                    if (count($validators_for_query) === count($params_tokens)) {
                        $parsed_query = array_combine(array_keys($validators_for_query), array_values($params_tokens));
                    }
                }
            }
    
            return $parsed_query;
        }
        catch (\Exception $e) {
            throw new Exception\RequestValidator($e);
        }
    }

    /**
     * @param Config\Interfaces\ItemRouter $RouteItem
     * @param array $get_data
     * @return array
     * @throws Exception\RequestValidator
     */
    protected function validateGet(Config\Interfaces\ItemRouter $RouteItem, array $get_data)
    {
        try {
            $parsed_get = [];
            $validators_for_get = $RouteItem->filterGetKeys($get_data);
            if (is_array($validators_for_get)) {
                foreach ($validators_for_get as $regex_name => $regex) {
                    $subject = $get_data[$regex_name];
                    $pattern = $this->regexCompleteAndValidate($RouteItem->getName(), $regex);
                    if (preg_match($pattern, $subject) === 1) {
                        $parsed_get[$regex_name] = $get_data[$regex_name];
                    }
                }
            }
    
            return $parsed_get;
        }
        catch (\Exception $e) {
           throw new Exception\RequestValidator($e);
        }        
    }

    /**
     * @param Config\Interfaces\ItemRouter $RouteItem
     * @param array $post_data
     * @return array
     * @throws Exception\RequestValidator
     */
    protected function validatePost(Config\Interfaces\ItemRouter $RouteItem, array $post_data)
    {
        try {
            foreach ($post_data as $param_name => $pvalue) {
                foreach ($RouteItem->getPostRegex() as $regex_name => $regex) {
                    if (strcasecmp($param_name, $regex_name) !== 0) {
                        continue;
                    }

                    $subject = $post_data[$param_name];
                    $pattern = $this->regexCompleteAndValidate($RouteItem->getName(), $regex);
                    if (preg_match($pattern, $subject, $params_tokens) === 0) {
                        unset($post_data[$param_name]);  //remove invalid post
                    }
                }
            }

            return $post_data;
        }
        catch (\Exception $e) {
            throw new Exception\RequestValidator($e);
        }
    }

    /**
     * @param array $validation_errors
     */
    public function setErrors($validation_errors)
    {
        $this->errors = $validation_errors;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return $this->errors === null;
    }
    
}