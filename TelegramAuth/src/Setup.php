<?php
class Setup
{
  public static function init()
  {
    $GLOBALS["wgPluggableAuth_ExtraLoginFields"] = [
      "telegram" => [
        "type" => "hidden",
        "value" => true,
      ],
    ];

    // $GLOBALS["wgDebugLogGroups"]["TelegramAuth"] =
    //   $GLOBALS['wgResourceBasePath'] . '/logs/TelegramAuth.log';
  }
}
