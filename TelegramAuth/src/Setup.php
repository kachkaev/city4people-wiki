<?php

class Setup
{
  public static function init()
  {
    $GLOBALS["wgPluggableAuth_ExtraLoginFields"] = [
      "telegram_id" => [
        "type" => "hidden",
        "value" => TelegramAuth::UNDEFINED_LOGIN_FIELD_VALUE,
      ],
      "telegram_first_name" => [
        "type" => "hidden",
        "value" => TelegramAuth::UNDEFINED_LOGIN_FIELD_VALUE,
      ],
      "telegram_last_name" => [
        "type" => "hidden",
        "value" => TelegramAuth::UNDEFINED_LOGIN_FIELD_VALUE,
      ],
      "telegram_username" => [
        "type" => "hidden",
        "value" => TelegramAuth::UNDEFINED_LOGIN_FIELD_VALUE,
      ],
      "telegram_photo_url" => [
        "type" => "hidden",
        "value" => TelegramAuth::UNDEFINED_LOGIN_FIELD_VALUE,
      ],
      "telegram_auth_date" => [
        "type" => "hidden",
        "value" => TelegramAuth::UNDEFINED_LOGIN_FIELD_VALUE,
      ],
      "telegram_hash" => [
        "type" => "hidden",
        "value" => TelegramAuth::UNDEFINED_LOGIN_FIELD_VALUE,
      ],
    ];
  }

  // https://www.mediawiki.org/wiki/Manual:Hooks/BeforePageDisplay
  public static function onBeforePageDisplay($out)
  {
    $out->addInlineStyle(
      <<<CSS
.mw-special-Userlogin button[name=pluggableauthlogin] {
  display: none !important;
}

.mw-special-Userlogin form[name=userlogin] > div {
  display: none;
}
.mw-special-Userlogin form[name=userlogin] > div.errorbox {
  display: block;
}

.mw-special-Userlogin #mw-content-text {
  padding-top: 60px;
  min-height: 50px;
}

.mw-special-Userlogin .telegram-container {
  overflow: visible;
  margin-top: -60px;
  height: 60px;
}
.mw-special-Userlogin .form-toggle-container {
  padding-bottom: 20px;
}
.mw-special-Userlogin .form-toggle-container span, .mw-special-Userlogin .form-toggle-container a {
  white-space: nowrap;
}
CSS
    );

    $out->addInlineScript(
      <<<JAVASCRIPT
window.onTelegramAuth = (user) => {
  const loginForm = $('form[name=userlogin]');
  
  const appendHiddenField = (key, value) => {
    loginForm.append($('<input />')
      .attr('type', 'hidden')
      .attr('name', key)
      .attr('value', value))
  }
  
  Object.entries(user).forEach(([key, value]) => {
    appendHiddenField("telegram_" + key, value);
  });
  appendHiddenField('pluggableauthlogin', true);
  loginForm.submit();
}

const init = () => {
  const loginForm = $('form[name=userlogin]');
  if (!loginForm.length) {
    return;
  }

  loginForm.before('<div class="telegram-container"><scr' + 'ipt async src="https://telegram.org/js/telegram-widget.js?11" data-telegram-login="city4people_wiki_bot" data-lang="ru" data-size="large" data-onauth="onTelegramAuth(user)" data-request-access="write"></' + 'sc' + 'ript></div>');

  const formToggleContainer = $('<div class="form-toggle-container"> <span>(для админов)</span></div>')
  const formToggle = $('<a href="#">Вход по логину и паролю</a>').click((e) => {
    e.preventDefault();
    loginForm.children().show();
    formToggleContainer.hide();
  });
  loginForm.after(formToggleContainer.prepend(formToggle));
}

RLQ.push(() => {
  const waitForJQueryInterval = setInterval(() => {
    if (typeof $ !== 'undefined') {
      clearInterval(waitForJQueryInterval)
      init()
    }
  }, 50);
});
JAVASCRIPT
    );
  }
}
