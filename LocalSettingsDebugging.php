<?php

error_reporting(-1);
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);

$wgShowExceptionDetails = true;
$wgDebugToolbar = true;
// $wgShowDebug=true;
$wgDevelopmentWarnings = true;
$wgDebugDumpSql = true;
$wgDebugLogFile = dirname(__FILE__) . "/debug.log";
$wgDebugComments = true;
$wgEnableParserCache = false;
$wgCachePages = false;
