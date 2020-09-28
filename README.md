# Вики Горпроектов

🚧🚧🚧
**Work in progress**
🚧🚧🚧

_**EN:** This repo contains setup instructions for a [MediaWiki](https://www.mediawiki.org/wiki/MediaWiki) instance that uses [Telegram login widget](https://core.telegram.org/widgets/login)_

## Ссылки по теме

### Телеграм-боты

[Bot Code Examples](https://core.telegram.org/bots/samples)

[Статья: Авторизация пользователей через Telegram](https://codex.so/telegram-auth)

## Разворачивание

### Необходимые условия

1.  Кластер на кубернетисе с настроенным подключением к нему

1.  Неймспейс `city4people-wiki`

    ```sh
    kubectl create namespace city4people-wiki
    ```

1.  Доступ к реестру с образами контейнеров

    См. <https://stackoverflow.com/a/61912590/1818285>

    ```sh
    GITHUB_USER=??
    GITHUB_TOKEN=??
    
    echo "{\"auths\":{\"docker.pkg.github.com\":{\"auth\":\"$(echo -n ${GITHUB_USER}:${GITHUB_TOKEN} | base64)\"}}}" | kubectl create secret generic dockerconfigjson-github-com --type=kubernetes.io/dockerconfigjson --from-file=.dockerconfigjson=/dev/stdin --namespace=city4people-wiki
    ```

### Телеграм-бот

```sh
TELEGRAM_BOT_TOKEN=??

kubectl create secret generic telegram-bot-token \
  --namespace=city4people-wiki \
  --from-literal=value=${TELEGRAM_BOT_TOKEN}

kubectl apply -f k8s/telegram-bot.yaml
```

### Вики-движок

- [README](https://hub.helm.sh/charts/bitnami/mediawiki)

- [values.yaml](https://github.com/bitnami/charts/blob/master/bitnami/mediawiki/values.yaml)

```sh
helm repo add bitnami https://charts.bitnami.com/bitnami

MEDIAWIKI_PASSWORD=??
MARIADB_ROOTUSER_PASSWORD=??

cat <<EOF >/tmp/values.yaml
service:
  type: ClusterIP
persistence:
  enabled: false
allowEmptyPassword: no
mediawikiEmail: alexander@kachkaev.ru
mediawikiHost: city4people-wiki.ru
mediawikiName: Вики Горпроектов
mediawikiPassword: "${MEDIAWIKI_PASSWORD}"
mediawikiUser: admin
mariadb:
  master:
    master:
      persistence:
        size: 10Gi
  rootUser:
    password: "${MARIADB_ROOTUSER_PASSWORD}"
persistence:
  size: 20Gi
EOF

## install
helm install --namespace=city4people-wiki mediawiki bitnami/mediawiki --values /tmp/values.yaml

## upgrade
helm upgrade mediawiki --namespace=city4people-wiki bitnami/mediawiki --values /tmp/values.yaml

## uninstall
helm uninstall --namespace=city4people-wiki mediawiki
```

```sh
kubectl apply -f k8s/mediawiki-ingress.yaml
```

### Шаги после создания сервера

### Заметки

Extensions:

- <https://www.mediawiki.org/wiki/Extension:Echo>
- <https://www.mediawiki.org/wiki/Extension:MobileFrontend>
- <https://www.mediawiki.org/wiki/Extension:StructuredDiscussions>
- <https://www.mediawiki.org/wiki/Extension:CirrusSearch>

Default user options: <https://www.mediawiki.org/wiki/Manual:$wgDefaultUserOptions>
`/opt/bitnami/mediawiki`

```php
// LocalSettings.php
$wgLanguageCode = "ru";

// Set timezone to Moscow
$wgLocaltimezone = "Europe/Moscow";
date_default_timezone_set( $wgLocaltimezone );

// Allow file uploads
$wgFileExtensions = array_merge( $wgFileExtensions, [ 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ods', 'odt' ] );

// Do not allow anonymous users to edit pages
$wgGroupPermissions['*']['edit'] = false;

// Restrict account creation
$wgGroupPermissions['*']['createaccount'] = false;
$wgGroupPermissions['*']['autocreateaccount'] = true;

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
$wgHiddenPrefs[] = 'skin';
$wgHiddenPrefs[] = 'date';
// $wgHiddenPrefs[] = 'password';

// Disable password resets
$wgPasswordResetRoutes = false;
$wgInvalidPasswordReset = false;

// https://www.mediawiki.org/wiki/Extension:WikiEditor
wfLoadExtension( 'WikiEditor' );
$wgHiddenPrefs[] = 'usebetatoolbar';

// Mobile styles https://www.mediawiki.org/wiki/Extension:MobileFrontend
wfLoadExtension( 'MobileFrontend' );
wfLoadSkin( 'MinervaNeue' );
$wgMFDefaultSkinClass = 'SkinMinerva';
$wgMinervaEnableSiteNotice = true;
$wgMinervaAlwaysShowLanguageButton = false;
$wgMinervaApplyKnownTemplateHacks = false;

// Fancy talk pages (TODO)
// https://www.mediawiki.org/wiki/Extension:StructuredDiscussions
// wfLoadExtension( 'ParserFunctions' );
// $wgContentHandlerUseDB = true;
// wfLoadExtension( 'Flow' );

// Logo and favicon
// 1.35
// $wgLogos = [
//  "1x" => "{$wgResourceBasePath}/images/city4people-wiki-logo.png",
//  "2x" => "{$wgResourceBasePath}/images/city4people-wiki-logo-x2.png",
// ]
$wgLogo = "{$wgResourceBasePath}/images/city4people-wiki-logo.png"; //135x135
$wgFavicon = "{$wgResourceBasePath}/images/city4people-wiki-favicon.png"; // 32x32
```

```wiki
<p style="text-align:left">
🚨🚨🚨<br>
'''Сайт в процессе настройки и пока ещё не готов к наполнению'''<br>
🚨🚨🚨
</p>

<p style="text-align:left">
Экспериментальная вики [https://city4people.ru Горпроектов] создаётся для координации работы в Пензенском отделении. Если всё получится, область применения расширится до федерального уровня. Вики Горпроектов — частная инициатива. По любым вопросам пишите в телеграм [https://t.me/kachkaev @kachkaev]
</p>
```

## Auth

List of login extensions: <https://www.mediawiki.org/wiki/Category:Login_extensions>

## Other extensions

<https://meta.miraheze.org/wiki/Special:Version>

## Notable Special pages

- Template:Infobox
- MediaWiki:Sitenotice

## Template debugging

`&uselang=qqx`
