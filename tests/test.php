<?php

namespace pietras;

require "vendor/autoload.php";

$filename = __DIR__ . "/../config/test.cfg";
$min = 1;
$max = 10;
var_dump(ScriptExecutor::randomMinuteFromFile($filename, $min, $max));
var_dump(file_get_contents($filename));
