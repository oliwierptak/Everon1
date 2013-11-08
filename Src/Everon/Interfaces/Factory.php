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
     * @param Interfaces\ConfigLoaderItem $ConfigLoaderItem
     * @param callable $Compiler
     * @return Interfaces\Config
     * @throws Exception\Factory
     */
    function buildConfig($name, Interfaces\ConfigLoaderItem $ConfigLoaderItem, \Closure $Compiler);

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
     * @param $filename
     * @param array $data
     * @return Config\Loader\Item
     * @throws Exception\Factory
     */
    function buildConfigLoaderItem($filename, array $data);

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
     * @param Interfaces\Config $Config
     * @param Interfaces\RouterValidator $Validator
     * @param string $ns
     * @return Interfaces\Router
     */
    function buildRouter(Interfaces\Config $Config, Interfaces\RouterValidator $Validator, $ns='Everon');

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
     * @param array $variables
     * @param string $ns
     * @return Interfaces\View
     * @throws Exception\Factory
     */
    function buildView($class_name, $template_directory, array $variables, $ns='Everon\View');

    /**
     * @param array $compilers_to_init
     * @param $view_directory
     * @return Interfaces\ViewManager
     * @throws Exception\Factory
     */
    function buildViewManager(array $compilers_to_init, $view_directory);

    /**
     * @param $directory
     * @param boolean $enabled
     * @return Interfaces\Logger
     * @throws Exception\Factory
     */
    function buildLogger($directory, $enabled);

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
