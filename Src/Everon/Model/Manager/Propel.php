<?php
namespace Everon\Model\Manager;

use Everon\Interfaces;
use Everon\Model;

class Propel extends Model\Manager implements Interfaces\ModelManager
{

    protected function init()
    {
        require_once ev_DIR_SRC.'Propel/runtime/lib/Propel.php';

        set_include_path(
            get_include_path().PATH_SEPARATOR.
                ev_DIR_MODEL.PATH_SEPARATOR.
                ev_DIR_MODEL.'om'.PATH_SEPARATOR.
                ev_DIR_MODEL.'map'.PATH_SEPARATOR
        );

        \Propel::init(ev_DIR_TMP.'propel'.DIRECTORY_SEPARATOR.'everon_book_catalog_example-conf.php');
    }

}
