<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Domain\Manager;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

use Everon\Interfaces;

class Doctrine extends Manager implements Interfaces\DomainManager
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
