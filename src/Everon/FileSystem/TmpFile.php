<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\FileSystem;

use Everon\Dependency;
use Everon\Exception;

class TmpFile implements Interfaces\TmpFile
{
    protected $handle = null;
    
    
    public function __construct()
    {
        $this->handle = tmpfile();
    }

    /**
     * @inheritdoc
     */
    public function write($content)
    {
        fwrite($this->handle, $content);
    }
    
    public function getFilename()
    {
        $meta = stream_get_meta_data($this->handle);
        return $meta['uri'];
    }
    
    public function close()
    {
        if ($this->handle !== null) {
            fclose($this->handle);
            $this->handle = null;
        }
    }
    
    public function __destruct()
    {
        $this->close();
    }
}
