<?php
namespace Everon;

include_once('Src/Everon/Lib/Bootstrap.php');
$Environment = new \Everon\Environment(PROJECT_ROOT);

function runTest($filename)
{
    if (is_file($filename) || is_dir($filename)) {
        $phpunit_xml = implode(DIRECTORY_SEPARATOR, ['Tests','Everon','phpunit.xml']);
        $coverage_dir = implode(DIRECTORY_SEPARATOR, ['Tests','Everon','coverage']);
        $cmd = 'phpunit -c '.$phpunit_xml.' --coverage-html '.$coverage_dir.' --verbose '.$filename;
        system($cmd);
    }
    else {
        die('Test file not found: '.$filename);
    }
}

function runTask($task)
{
    system($task);
}

if ($argc > 1) {
    array_shift($argv);
    $task = $argv[0];

    array_shift($argv);
    $params = $argv;

    switch (strtolower($task)){
        case 'test':
            $filename = trim(@$params[0]);
            runTest($filename);
            break;

        case 'propel-gen':
            chdir($Environment->getConfig().'propel'.DIRECTORY_SEPARATOR);
            $propel_gen = ev_OS_UNIX ? 'propel-gen' : 'propel-gen.bat';
            $task = $Environment->getSource().implode(DIRECTORY_SEPARATOR, ['Propel', 'generator','bin', $propel_gen]).' '.implode(' ', $params);
            runTask($task);
            break;

        case 'help':
            echo('
Usage: php Everon.php [task]

Tests/<path>             Anything starting with "Tests" and pointing to a file or directory
                         defaults to phpunit, eg. "php Everon.php Tests/Everon/unit"
                         
propel-gen               Wrapper for propel-gen
  
');
            break;

        default:
            if (strtolower(substr($task, 0, strlen('tests'))) == 'tests'){
                runTest($task);
            }
            else {
                echo("Unknown task. Try php Everon.php help\n");
            }
            break;
    }
}
