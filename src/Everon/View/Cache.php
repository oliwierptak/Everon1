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
    use Dependency\Injection\Logger;
    use Dependency\FileSystem;
    use Helper\RunPhp;

    /**
     * @var Interfaces\Collection
     */
    protected $Repository = null;
    
    public function __construct(Interfaces\FileSystem $FileSystem)
    {
        $this->FileSystem = $FileSystem;
    }
    
    public function getRepository()
    {
        if ($this->Repository === null) {
            $this->Repository = new Helper\Collection([]);
        }
        
        return $this->Repository;
    }
    
    public function handle(Interfaces\ViewManager $ViewManager, Interfaces\View $View, $action)
    {
        $Template = $View->getContainer();
        $Filename = $View->getFilename();
        $Repository = $this->getRepository();
        
        $cache_filename = '//'.$View->getName().DIRECTORY_SEPARATOR.$action.'.php';
        $data_filename = '//'.$View->getName().DIRECTORY_SEPARATOR.$action.'.cache.php';
        
        $CacheFile = $this->getFileSystem()->load($cache_filename);
        $DataFile = $this->getFileSystem()->load($data_filename);
        
        if ($CacheFile !== null && $DataFile !== null) {
            
            $TmpDataFile = $this->getFileSystem()->createTmpFile();
            $TmpDataFile->write($DataFile);
            $php_file = $TmpDataFile->getFilename();

            include $php_file; //load $cache

            $Template->setTemplateContent($CacheFile);
            $Template->setData($cache);
            $Template->setCompiledContent($this->runPhp($CacheFile, $cache, $this->getFileSystem())); //todo: add try/catch remove e_error from runPhp 
            
            $TmpDataFile->close();
            
            return;
        }
        
        if ($Repository->has($Filename->getPathname())) {
            $View->setContainer($Repository->get($Filename->getPathname()));
        }
        else {
            $ViewManager->compileTemplate($View->getName(), $Template);
            $Scope = $Template->getScope();
            $content = $Scope->getPhp();
            
            $this->getFileSystem()->save($cache_filename, $content);

            $data = var_export($Scope->getData(), true);
            $this->getFileSystem()->save($data_filename, "<?php \$cache = $data; ");

            $Repository->set($cache_filename, $Template);
        }
    }
}