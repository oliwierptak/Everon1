<?php
namespace Everon\Model\Manager;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

use Everon\Interfaces;
use Everon\Model;

class Doctrine extends Model\Manager implements Interfaces\ModelManager
{
    protected function init()
    {
        require_once ev_DIR_SRC.\ev_fixPath("Doctrine\\vendor\\autoload.php");

        $paths = array("/path/to/entities-or-mapping-files");
        $isDevMode = true;

        // the connection configuration
        $dbParams = array(
            'driver'   => 'pdo_mysql',
            'user'     => 'root',
            'password' => '',
            'dbname'   => 'foo',
        );

        $config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
        $entityManager = EntityManager::create($dbParams, $config);
    }
}
