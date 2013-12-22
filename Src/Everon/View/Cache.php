<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\View;

use Everon\Dependency;
use Everon\Exception;
use Everon\Helper;
use Everon\Interfaces;

class Cache
{

    /**
     * @var Interfaces\Collection
     */
    protected $Repository = null;
    
    public function __construct($cache_directory)
    {
        $this->cache_directory = $cache_directory;
    }
    
    
    public function getRepository()
    {
        if ($this->Repository === null) {
            $this->Repository = new Helper\Collection([]);
        }
        
        return $this->Repository;
    }
    
    public function handle(Interfaces\ViewManager $ViewManager, Interfaces\View $View)
    {
        $Template = $View->getContainer();
        $Filename = $View->getFilename();
        $Repository = $this->getRepository();
        
        if ($Repository->has($Filename->getPathname())) {
            
        }
        else {
            $CacheFilename = new \SplFileInfo($this->cache_directory.$View->getName().DIRECTORY_SEPARATOR.$Filename->getBasename());
            $CacheDataFilename = new \SplFileInfo($this->cache_directory.$View->getName().DIRECTORY_SEPARATOR.$Filename->getBasename().'.cache.php');
            $ViewManager->compileTemplate($View->getName(), $Template);

            $content = (string) $View->getContainer();

            $h = fopen($CacheFilename->getPathname(), 'w');
            fwrite($h, $content);
            fclose($h);

            $data = var_export($View->getData(), true);
            $h = fopen($CacheDataFilename->getPathname(), 'w');
            fwrite($h, "<?php \$cache = $data; ");
            fclose($h);
        }
    }
}