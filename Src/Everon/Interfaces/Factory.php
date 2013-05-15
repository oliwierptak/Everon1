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
     */
    function buildCore();

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
     * @param Interfaces\View $View
     * @param Interfaces\ModelManager $ModelManager
     * @param string $ns
     * @return Controller
     * @throws Exception\Factory
     */
    function buildController($class_name, Interfaces\View $View, Interfaces\ModelManager $ModelManager, $ns='\Everon\Controller');

    function buildModel($class_name);

    /**
     * @param $class_name
     * @param string $ns
     * @return Interfaces\ModelManager
     * @throws Exception\Factory
     */
    function buildModelManager($class_name, $ns='Everon\Model\Manager');

    /**
     * @param array $data
     * @return Interfaces\ConfigItemRouter
     */
    function buildConfigItemRouter(array $data);

    /**
     * @param array $data
     * @return Interfaces\ConfigItemView
     * @throws Exception\Factory
     */
    function buildConfigItemView(array $data);

    /**
     * @param Request $Request
     * @param ConfigRouter $Config
     * @param RouterValidator $Validator
     * @return Interfaces\Router
     */
    function buildRouter(Request $Request, ConfigRouter $Config, Interfaces\RouterValidator $Validator);

    /**
     * @return Interfaces\RouterValidator
     * @throws Exception\Factory
     */
    function buildRouterValidator();    

    /**
     * @param View $View
     * @param $filename
     * @param array $template_data
     * @return Interfaces\TemplateContainer
     */
    function buildTemplate(View $View, $filename, array $template_data);

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
     * @param array $compilers_to_init
     * @param $view_template_directory
     * @param string $ns
     * @return Interfaces\View
     */
    function buildView($class_name, array $compilers_to_init, $view_template_directory, $ns='Everon\View');

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
     * @param null $data
     * @param Interfaces\Collection $Headers
     * @return Interfaces\Response
     * @throws Exception\Factory
     */
    function buildResponse($data=null, Interfaces\Collection $Headers=null);

    /**
     * @param array $server
     * @param array $get
     * @param array $post
     * @param array $files
     * @return Interfaces\Request
     * @throws Exception\Factory
     */
    function buildRequest(array $server, array $get, array $post, array $files);
    
    
}
