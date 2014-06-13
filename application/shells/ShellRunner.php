<?php

include_once('../../framework/Lighter.php');

Lib::Import('shells/AppShell');

if($argc < 2) {
    die("Syntax: php ShellRunner.php <shell class name> [option] [option]...");
}

$shell_name = preg_replace('/\\.php$/', '', $argv[1]);

$arguments = array_slice($argv, 2);

Lib::Import('shells/' . $shell_name);

/**
 * @var AppShell $shell
 */
$shell = new $shell_name();

ModelCache::$singleton->is_enabled = false;
$shell->main($arguments);
ModelCache::$singleton->is_enabled = true;