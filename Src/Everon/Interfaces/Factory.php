<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Interfaces;

use Everon\Config;
use Everon\Interfaces;
use Everon\Exception;

interface Factory
{
    /**
     * @return Interfaces\DependencyContainer
     */
    public function getDependencyContainer();
    
    /**
     * @param Interfaces\DependencyContainer $Container
     */
    function setDependencyContainer(Interfaces\DependencyContainer $Container);

    /**
     * @return Interfaces\Core
     * @throws Exception\Factory
     */
    function buildConsole();

    /**
     * @return Interfaces\Core
     * @throws Exception\Factory
     */
    function buildMvc();    

    /**
     * @param $name
     * @param $filename
     * @param $data
     * @return Interfaces\Config
     * @throws Exception\Factory
     */
    public function buildConfig($name, $filename, $data);

    /**
     * @param Interfaces\ConfigLoader $Loader
     * @param Interfaces\ConfigExpressionMatcher $Matcher
     * @return Config\Manager|mixed
     * @throws Exception\Factory
     */
    public function buildConfigManager(Interfaces\ConfigLoader $Loader, Interfaces\ConfigExpressionMatcher $Matcher);

    /**
     * @return Interfaces\ConfigExpressionMatcher
     * @throws Exception\Factory
     */
    function buildConfigExpressionMatcher();

    /**
     * @param $config_directory
     * @param $cache_directory
     * @return Config\Loader
     * @throws Exception\Factory
     */
    function buildConfigLoader($config_directory, $cache_directory);

    /**
     * @param $class_name
     * @param string $ns
     * @return Interfaces\Controller
     * @throws Exception\Factory
     */
    function buildController($class_name, $ns='Everon\Controller');

    function buildModel($class_name);

    /**
     * @param $class_name
     * @param string $ns
     * @return Interfaces\ModelManager
     * @throws Exception\Factory
     */
    function buildModelManager($class_name, $ns='Everon\Model\Manager');

    /**
     * @param $name
     * @param array $data
     * @return Interfaces\ConfigItem
     * @throws Exception\Factory
     */
    function buildConfigItem($name, array $data);

    /**
     * @param $name
     * @param array $data
     * @return Interfaces\ConfigItemRouter
     * @throws Exception\Factory
     */
    function buildConfigItemRouter($name, array $data);

    /**
     * @param array $name
     * @param array $data
     * @return Interfaces\ConfigItem
     * @throws Exception\Factory
     */
    function buildConfigItemView($name, array $data);

    /**
     * @param Interfaces\Config $Config
     * @param Interfaces\RouterValidator $Validator
     * @return Interfaces\Router
     */
    function buildRouter(Interfaces\Config $Config, Interfaces\RouterValidator $Validator);

    /**
     * @return Interfaces\RouterValidator
     * @throws Exception\Factory
     */
    function buildRouterValidator();

    /**
     * @param $filename
     * @param array $template_data
     * @return Interfaces\TemplateContainer
     * @throws Exception\Factory
     */
    function buildTemplate($filename, array $template_data);

    /**
     * @param $template_string
     * @param array $template_data
     * @return Interfaces\TemplateContainer
     */
    function buildTemplateContainer($template_string, array $template_data);

    /**
     * @param $name
     * @return Interfaces\TemplateCompiler
     */
    function buildTemplateCompiler($name);

    /**
     * @param $class_name
     * @param $template_directory
     * @param string $ns
     * @return Interfaces\View
     * @throws Exception\Factory
     */
    function buildView($class_name, $template_directory, $ns='Everon\View');

    /**
     * @param $compilers_to_init
     * @param $view_template_directory
     * @param $view_cache_directory
     * @return Interfaces\ViewManager
     * @throws Exception\Factory
     */
    public function buildViewManager($compilers_to_init, $view_template_directory, $view_cache_directory);

    /**
     * @param $directory
     * @return Interfaces\Logger
     * @throws Exception\Factory
     */
    function buildLogger($directory);

    /**
     * @param array $headers
     * @return Interfaces\Collection
     * @throws Exception\Factory
     */    
    function buildHttpHeaderCollection(array $headers=[]);

    /**
     * @param Interfaces\Collection $Headers
     * @return Interfaces\Response
     * @throws Exception\Factory
     */
    function buildResponse(Interfaces\Collection $Headers);

    /**
     * @param array $server
     * @param array $get
     * @param array $post
     * @param array $files
     * @return Interfaces\Request
     * @throws Exception\Factory
     */
    function buildRequest(array $server, array $get, array $post, array $files);

    /**
     * @param array $server
     * @param array $get
     * @param array $post
     * @param array $files
     * @return Interfaces\Request
     * @throws Exception\Factory
     */
    public function buildConsoleRequest(array $server, array $get, array $post, array $files);    

    /**
     * @param $root
     * @return Interfaces\Environment
     * @throws Exception\Factory
     */
    function buildEnvironment($root);    
}
