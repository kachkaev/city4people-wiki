<?php
// Imports
use MediaWiki\Auth\AuthManager;
use MediaWiki\Auth\AuthenticationRequest;
use MediaWiki\MediaWikiServices;
use MediaWiki\Session\SessionManager;

class TelegramAuth extends PluggableAuth
{
  const UNDEFINED_LOGIN_FIELD_VALUE = "~~~~~~~~UNDEFINED~~~~~~~~";

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
      ->makeConfig("TelegramAuth"); // Get the config
    $botTokenSha256 = hex2bin($config->get("TelegramAuth_BotTokenSha256"));

    // AuthManager for managing the session
    $authManager = AuthManager::singleton();

    try {
      $extraLoginFields = $authManager->getAuthenticationSessionData(
        PluggableAuthLogin::EXTRALOGINFIELDS_SESSION_KEY
      );

      $telegramId = $extraLoginFields["telegram_id"];
      $telegramFirstName = $extraLoginFields["telegram_first_name"];
      $telegramLastName = $extraLoginFields["telegram_last_name"];
      $telegramUsername = $extraLoginFields["telegram_username"];
      $telegramPhotoUrl = $extraLoginFields["telegram_photo_url"];
      $telegramAuthDate = $extraLoginFields["telegram_auth_date"];

      // Check telegram data hash
      // https://gist.github.com/anonymous/6516521b1fb3b464534fbc30ea3573c2
      $dataCheckArray = [];
      foreach ($extraLoginFields ?: [] as $key => $value) {
        if (
          substr($key, 0, 9) === "telegram_" &&
          $key !== "telegram_hash" &&
          $value !== self::UNDEFINED_LOGIN_FIELD_VALUE
        ) {
          $dataCheckArray[] = substr($key, 9) . "=" . $value;
        }
      }
      sort($dataCheckArray);
      $dataCheckString = implode("\n", $dataCheckArray);

      $hash = hash_hmac("sha256", $dataCheckString, $botTokenSha256);
      if (strcmp($hash, $extraLoginFields["telegram_hash"]) !== 0) {
        $errorMessage =
          "Что-то не так с целостностью данных, полученных от Телеграма. Попробуйте обновить страницу и повторить попытку";
        wfDebugLog(
          "TelegramAuth",
          "hash miss for user $telegramUsername (id $telegramId)"
        );
        return false;
      }

      // Ensure only users with telegram logins can log in
      if (
        $telegramUsername === self::UNDEFINED_LOGIN_FIELD_VALUE ||
        strlen($telegramUsername) < 1
      ) {
        $errorMessage =
          "В вашей учётной записи не указано имя пользователя. Пожалуйста, зайдите в настройки Телеграма, создайте для себя имя пользователя, а затем повторите попытку.";
        return false;
      }

      $username = $telegramUsername;

      $user = User::newFromName($username);
      $user_id = $user->getId() === 0 ? null : $user->getId();
      $id = $user_id === 0 ? null : $user_id;

      return true;
    } catch (Exception $e) {
      // Log if something goes wrong
      wfDebugLog("TelegramAuth", "exception" . $e->__toString() . PHP_EOL);
      $errorMessage = $e->__toString();
      return false;
    } catch (Error $e) {
      // Log if something goes wrong
      wfDebugLog("TelegramAuth", "error" . $e->__toString() . PHP_EOL);
      $errorMessage = $e->__toString();
      return false;
    }
  }

  public function deauthenticate(User &$user)
  {
    return true;
  }

  public function saveExtraAttributes($id)
  {
  }

  public static function onPluggableAuthPopulateGroups(User $user)
  {
    // if ($result === false) {
    //   return false;
    // }

    if (!isset($GLOBALS["wgTelegramAuth_AutoPopulateGroups"])) {
      return false;
    }

    // Subtract the groups the user already has from the list of groups to populate.
    $populateGroups = array_diff(
      (array) $GLOBALS["wgTelegramAuth_AutoPopulateGroups"],
      $user->getEffectiveGroups()
    );

    foreach ($populateGroups as $group) {
      $user->addGroup($group);
    }

    return true;
  }
}
