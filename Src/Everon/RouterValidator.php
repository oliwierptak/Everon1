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

class RouterValidator implements Interfaces\RouterValidator
{
    use Helper\Asserts;
    use Helper\Asserts\IsArrayKey;    
    use Helper\Regex;

    /**
     * Throws exception on error, returns array of validated get and post otherwise
     * 
     * @param Interfaces\ConfigItemRouter $RouteItem
     * @param Interfaces\Request $Request
     * @return array
     */
    public function validate(Interfaces\ConfigItemRouter $RouteItem, Interfaces\Request $Request)
    {
        $parsed_get_parameters = $this->validateQueryAndGet($RouteItem, $Request->getUrl(), $Request->getQueryCollection());
        $this->validateRoute(
            $RouteItem->getName(),
            (array) $RouteItem->getGetRegex(),
            $parsed_get_parameters
        );
        
        $parsed_post_parameters = $this->validatePost($RouteItem, $Request->getPostCollection());
        $this->validateRoute(
            $RouteItem->getName(),
            (array) $RouteItem->getPostRegex(),
            $parsed_post_parameters
        );        
        
        return [$parsed_get_parameters, $parsed_post_parameters];
    }

    /**
     * @param $route_name
     * @param array $route_params
     * @param array $parsed_request_params
     * @throws Exception\InvalidRouterParameter
     */
    protected function validateRoute($route_name, array $route_params, array $parsed_request_params)
    {
        foreach ($route_params as $name => $expression) {
            $this->assertIsArrayKey($name, $parsed_request_params,
                vsprintf('Invalid required parameter: "%s" for route: "%s"', [$name, $route_name]),
                'InvalidRouterParameter'
            );
        }
    }

    /**
     * Matches urls like /news/show/12 with /news/show/{id} and returns ['id' => 12]
     * Also checks if $_GET values are set according to the regex in router.ini
     *
     * Returns merged data from parsed query string and _GET
     *
     * @param Interfaces\ConfigItemRouter $RouteItem
     * @param $request_url
     * @param $get_data
     * @return array|null
     * @throws Exception\Router
     */
    protected function validateQueryAndGet(Interfaces\ConfigItemRouter $RouteItem, $request_url, array $get_data)
    {
        try {
            $parsed_query = $this->validateQuery($RouteItem, $request_url, $get_data);
            $parsed_get = $this->validateGet($RouteItem, $get_data);
            return array_merge($parsed_query, $parsed_get);
        }
        catch (\Exception $e) {
            throw new Exception\Router($e);
        }
    }

    /**
     * @param Interfaces\ConfigItemRouter $RouteItem
     * @param $request_url
     * @param array $get_data
     * @return array
     */
    protected function validateQuery(Interfaces\ConfigItemRouter $RouteItem, $request_url, array $get_data)
    {
        $request_url = $RouteItem->getCleanUrl($request_url);
        $regex_url = $RouteItem->getCleanUrl($RouteItem->getUrl());

        $parsed_query = [];
        $validators_for_query = $RouteItem->filterQueryKeys($get_data);
        if (is_array($validators_for_query)) {
            $url_pattern = $RouteItem->replaceCurlyParametersWithRegex($regex_url, $validators_for_query);
            $url_pattern = $this->regexCompleteAndValidate($RouteItem->getName(), $url_pattern);

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
     * @param Interfaces\ConfigItemRouter $RouteItem
     * @param array $get_data
     * @return array
     */
    protected function validateGet(Interfaces\ConfigItemRouter $RouteItem, array $get_data)
    {
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

    /**
     * @param Interfaces\ConfigItemRouter $RouteItem
     * @param array $post_data
     * @return array
     * @throws Exception\Router
     */
    protected function validatePost(Interfaces\ConfigItemRouter $RouteItem, array $post_data)
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
            throw new Exception\Router($e);
        }
    }

}