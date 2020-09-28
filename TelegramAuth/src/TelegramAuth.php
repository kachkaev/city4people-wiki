<?php
// Imports
use MediaWiki\Auth\AuthManager;
use MediaWiki\Auth\AuthenticationRequest;
use MediaWiki\MediaWikiServices;
use MediaWiki\Session\SessionManager;

class TelegramAuth extends PluggableAuth
{
  public function authenticate(
    &$id,
    &$username,
    &$realname,
    &$email,
    &$errorMessage
  ) {
    // Get config options
    $config = MediaWikiServices::getInstance()
      ->getConfigFactory()
      ->makeConfig('TelegramAuth'); // Get the config
    $botTokenSha256 = $config->get('TelegramAuth_BotTokenSha256');

    // AuthManager for managing the session
    $authManager = AuthManager::singleton();

    wfDebugLog('PluggableAuth', 'AAAAA');

    wfDebugLog('TelegramAuth', "Hey" . $botTokenSha256 . PHP_EOL);

    try {
      $request = $authManager->getRequest();

      $sessData = $authManager->getAuthenticationSessionData(
        PluggableAuthLogin::RETURNTOURL_SESSION_KEY
      );

      wfDebugLog(
        'TelegramAuth',
        "Sess " . $request->getSession()->getAllowedUserRights()
      );

      $username = 'Telegram_' . substr($botTokenSha256, 0, 20);
      $realname = "Hello User";
      $id = 10000042;

      return true;
    } catch (Exception $e) {
      // Log if something goes wrong
      wfDebugLog('TelegramAuth', "exception" . $e->__toString() . PHP_EOL);
      $errorMessage = $e->__toString();
      return false;
    } catch (Error $e) {
      // Log if something goes wrong
      wfDebugLog('TelegramAuth', "error" . $e->__toString() . PHP_EOL);
      $errorMessage = $e->__toString();
      return false;
    }

    // try {
    //   // Create LightOpenID that directs back to this session
    //   $openid = new LightOpenID(
    //     $authManager->getAuthenticationSessionData(
    //       PluggableAuthLogin::RETURNTOURL_SESSION_KEY
    //     )
    //   );

    //   // If the LightOpenID hasn't started send the user to Steam (eventually display the login page)
    //   if (!$openid->mode) {
    //     // Check if the button was clicked
    //     $isLoggingIn = $authManager->getAuthenticationSessionData(
    //       PluggableAuthLogin::EXTRALOGINFIELDS_SESSION_KEY
    //     );

    //     if (isset($isLoggingIn['steam'])) {
    //       $openid->identity = 'https://steamcommunity.com/openid/?l=english'; // Force english so a random lang is not selected
    //       header('Location: ' . $openid->authUrl());
    //       exit();
    //     } else {
    //       // Show the login page
    //       $errorMessage = '';
    //       return false;
    //     }
    //   } elseif ($openid->mode == 'cancel') {
    //     // Tell the user they canceled auth
    //     $errorMessage = 'User has canceled authentication.';
    //     return false;
    //   } else {
    //     // Validate the login and sign the user in
    //     if ($openid->validate()) {
    //       $sidurl = $openid->identity; // Steam ID Url

    //       // Get only the ID of the user
    //       $ptn =
    //         "/^https:\/\/steamcommunity\.com\/openid\/id\/(7[0-9]{15,25}+)\/?$/";
    //       preg_match($ptn, $sidurl, $matches);

    //       // Get the user's info
    //       $url =
    //         "https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=" .
    //         $steamapi .
    //         "&steamids=" .
    //         $matches[1] .
    //         "&format=json";
    //       $json_object = file_get_contents($url);
    //       $json_decoded = json_decode($json_object);

    //       $player = $json_decoded->response->players[0];

    //       // Check if the appid has been specified
    //       if ($appid != null) {
    //         // If the appid has been specified look if the user has it
    //         $hasgame = false; // Has game var

    //         // Get the users games
    //         $url =
    //           "https://api.steampowered.com/IPlayerService/GetOwnedGames/v0001/?key=" .
    //           $steamapi .
    //           "&steamid=" .
    //           $matches[1] .
    //           "&format=json&include_played_free_games=true";
    //         $json_object = file_get_contents($url);
    //         $json_decoded = json_decode($json_object);

    //         // If a game has the appid update the hasgame var
    //         $games = $json_decoded->response->games;
    //         foreach ($games as &$game) {
    //           if ($game->appid == $appid) {
    //             $hasgame = true;
    //           }
    //         }

    //         // Pass or fail login
    //         if ($hasgame) {
    //           // Log user in if they have the game
    //           $id = $player->steamid;
    //           $username = $player->steamid;
    //           $realname = $player->personaname;

    //           return true;
    //         } else {
    //           // Don't log the user in if they dont have the game
    //           $errorMessage =
    //             "User does not have the correct game. (Please make sure your \"Game Details\" are set to public)";
    //           return false;
    //         }
    //       } else {
    //         // If the appid has not been specified then log the user in
    //         $id = $player->steamid;
    //         $username = $player->steamid;
    //         $realname = $player->personaname;

    //         return true;
    //       }
    //     } else {
    //       // If the login wasn't valid tell the user
    //       $errorMessage = 'User is not logged in.';
    //       return false;
    //     }
    //   }
    // } catch (Exception $e) {
    //   // Log if something goes wrong
    //   wfDebugLog('Steam Auth', $e->__toString() . PHP_EOL);
    //   $errorMessage = $e->__toString();
    //   return false;
    // }
  }

  public function deauthenticate(User &$user)
  {
    return true;
  }

  public function saveExtraAttributes($id)
  {
  }
}
