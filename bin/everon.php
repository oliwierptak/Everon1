<?php
// #!/usr/bin/env php
namespace Everon;

require_once(
    implode(DIRECTORY_SEPARATOR, [dirname(__FILE__), '..', '..','..','..', 'Config', 'Bootstrap', 'console.php'])
);

$Console = $Factory->buildConsole();
$Console->run($RequestIdentifier);

