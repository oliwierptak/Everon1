<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Helper;

/**
 * @method \Everon\Config\Interfaces\Manager getConfigManager
 */
trait GetUrl 
{
    /**
     * @inheritdoc
     */
    public function getUrl($name, $query=[], $get=[])
    {
        $Item = $this->getConfigManager()->getConfigByName('router')->getItemByName($name);
        if ($Item === null) {
            throw new \Everon\Exception\Application('Invalid router config name for url: "%s"', (string) $name);
        }
        
        $Item->compileUrl($query);
        $url = $Item->getParsedUrl();

        $get_url = '';
        if (empty($get) === false) {
            $get_url = http_build_query($get);
            if (trim($get_url) !== '') {
                $get_url = '?'.$get_url;
            }
        }

        return $url.$get_url;
    }
}