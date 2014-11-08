<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Config\Loader;

use Everon\Config\Interfaces;
use Everon\Helper;

class Item implements Interfaces\LoaderItem
{
    use Helper\ToArray;

    protected $filename = null;

    protected $is_caching_enabled = false;


    /**
     * @param $filename
     * @param array $data
     * @param $use_cache
     */
    public function __construct($filename, array $data, $use_cache)
    {
        $this->filename = $filename;
        $this->data = $data;
        $this->is_caching_enabled = $use_cache;
    }

    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @inheritdoc
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return boolean
     */
    public function isCachingEnabled()
    {
        return $this->is_caching_enabled;
    }

    /**
     * @param boolean $use_cache
     */
    public function setIsCachingEnabled($use_cache)
    {
        $this->is_caching_enabled = $use_cache;
    }
}