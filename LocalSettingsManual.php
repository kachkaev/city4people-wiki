<?php

$wgLanguageCode = "ru";

// Set timezone to Moscow
$wgLocaltimezone = "Europe/Moscow";
date_default_timezone_set($wgLocaltimezone);

// Allow file uploads
$wgFileExtensions = array_merge($wgFileExtensions, [
  'doc',
  'docx',
  'ods',
  'odt',
  'pdf',
  'svg',
  'xls',
  'xlsx',
]);

// Do not allow anonymous users to edit pages
$wgGroupPermissions['*']['edit'] = false;

// Disable user mailing
$wgEnableEmail = false;
$wgEnableUserEmail = false;
$wgHiddenPrefs[] = 'disablemail';

// Disable some user preferences
// https://www.mediawiki.org/wiki/Manual:$wgDefaultUserOptions
$wgHiddenPrefs[] = 'language';
$wgHiddenPrefs[] = 'realname';
$wgHiddenPrefs[] = 'fancysig';
$wgHiddenPrefs[] = 'nickname';
// $wgHiddenPrefs[] = 'password';
$wgHiddenPrefs[] = 'skin';
$wgHiddenPrefs[] = 'date';

// Disable password resets
$wgPasswordResetRoutes = false;
$wgInvalidPasswordReset = false;

// https://www.mediawiki.org/wiki/Extension:WikiEditor
wfLoadExtension('WikiEditor');
$wgHiddenPrefs[] = 'usebetatoolbar';

// Mobile styles https://www.mediawiki.org/wiki/Extension:MobileFrontend
wfLoadExtension('MobileFrontend');
wfLoadSkin('MinervaNeue');
$wgMFDefaultSkinClass = 'SkinMinerva';
$wgMinervaEnableSiteNotice = true;

// Auth via Telegram
wfLoadExtension('PluggableAuth');
$wgPluggableAuth_EnableLocalLogin = false;
wfLoadExtension('TelegramAuth');
$wgTelegramAuth_BotTokenSha256 =
  "28f30cc7c42894e47eceebc3e74ab76dc69ff639045ea473f5ffaa0c7bb5c959";

// TMP
$wgPluggableAuth_EnableLocalLogin = true;

// Restrict account creation
$wgGroupPermissions['*']['createaccount'] = false;
$wgGroupPermissions['*']['autocreateaccount'] = true;

// Logo and favicon
// $wgLogos = [
// 	"1x" => "{$wgResourceBasePath}/images/city4people-wiki-logo.png",
// 	"2x" => "{$wgResourceBasePath}/images/city4people-wiki-logo-x2.png",
// ]
$wgLogo = "{$wgResourceBasePath}/images/city4people-wiki-logo.png"; //135x135
$wgFavicon = "{$wgResourceBasePath}/images/city4people-wiki-favicon.png"; // 32x32
