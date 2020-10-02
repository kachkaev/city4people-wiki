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
    # GITHUB_USER=
    # GITHUB_TOKEN=
    
    echo "{\"auths\":{\"docker.pkg.github.com\":{\"auth\":\"$(echo -n ${GITHUB_USER}:${GITHUB_TOKEN} | base64)\"}}}" | kubectl create secret generic dockerconfigjson-github-com --type=kubernetes.io/dockerconfigjson --from-file=.dockerconfigjson=/dev/stdin --namespace=city4people-wiki
    ```

### Телеграм-бот

```sh
INSTANCE=main
INSTANCE_HOST=city4people-wiki.ru
# TELEGRAM_BOT_TOKEN=
TELEGRAM_BOT_USERNAME=city4people_wiki_bot

INSTANCE=sandbox
INSTANCE_HOST=sandbox.city4people-wiki.ru
# TELEGRAM_BOT_TOKEN=
TELEGRAM_BOT_USERNAME=sandbox_wiki_bot

IMAGE_TAG=v2020092309

cat <<EOF >/tmp/values-for-telegram-bot.yaml
botDomain: "${INSTANCE_HOST}"
botToken: "${TELEGRAM_BOT_TOKEN}"
botUsername: ${TELEGRAM_BOT_USERNAME}
image:
  tag: ${IMAGE_TAG}
imagePullSecrets:
  - name: dockerconfigjson-github-com
resources:
  requests:
    cpu: 10m
    memory: 100Mi
  limits:
    cpu: 1000m
    memory: 200Mi
EOF

## install
helm install --namespace=city4people-wiki "${INSTANCE}-telegram-bot" ./charts/telegram-bot --values /tmp/values-for-telegram-bot.yaml

## upgrade
helm upgrade --namespace=city4people-wiki "${INSTANCE}-telegram-bot" ./charts/telegram-bot --values /tmp/values-for-telegram-bot.yaml

## uninstall
helm uninstall --namespace=city4people-wiki "${INSTANCE}-telegram-bot"
```

### Вики-движок

- [README](https://hub.helm.sh/charts/bitnami/mediawiki)

- [values.yaml](https://github.com/bitnami/charts/blob/master/bitnami/mediawiki/values.yaml)

```sh
helm repo add bitnami https://charts.bitnami.com/bitnami

INSTANCE=main
INSTANCE_HOST=city4people-wiki.ru
MEDIAWIKI_NAME="Вики Горпроектов"
# MEDIAWIKI_PASSWORD=
# MARIADB_ROOTUSER_PASSWORD=

cat <<EOF >/tmp/values-for-webapp.yaml
# nameOverride: ${INSTANCE}-mediawiki
image:
  tag: 1.35.0-debian-10-r8
service:
  type: ClusterIP
persistence:
  accessMode: ReadWriteMany
  enabled: true
  size: 20Gi
allowEmptyPassword: no
mediawikiEmail: alexander@kachkaev.ru
mediawikiHost: ${INSTANCE_HOST}
mediawikiName: ${MEDIAWIKI_NAME}
mediawikiPassword: "${MEDIAWIKI_PASSWORD}"
mediawikiUser: admin
mariadb:
  # nameOverride: ${INSTANCE}-mariadb
  master:
    persistence:
      accessModes:
        - ReadWriteMany
      enabled: true
      size: 10Gi
  rootUser:
    password: "${MARIADB_ROOTUSER_PASSWORD}"
EOF

## install
helm install --namespace=city4people-wiki "${INSTANCE}-webapp" bitnami/mediawiki --values /tmp/values-for-webapp.yaml

## upgrade
helm upgrade --namespace=city4people-wiki "${INSTANCE}-webapp" bitnami/mediawiki --values /tmp/values-for-webapp.yaml

## uninstall
helm uninstall --namespace=city4people-wiki "${INSTANCE}-webapp"
```

#### Ingress

```sh
INSTANCE=main
INSTANCE_HOST=city4people-wiki.ru
SERVICE_NAME=main-webapp-mediawiki

INSTANCE=sandbox
INSTANCE_HOST=sandbox.city4people-wiki.ru
SERVICE_NAME=mediawiki

cat <<EOF >/tmp/values-for-webapp-ingress.yaml
serviceName: ${SERVICE_NAME}
host: ${INSTANCE_HOST}
EOF

## install
helm install --namespace=city4people-wiki "${INSTANCE}-webapp-ingress" ./charts/webapp-ingress --values /tmp/values-for-webapp-ingress.yaml

## upgrade
helm upgrade --namespace=city4people-wiki "${INSTANCE}-webapp-ingress" ./charts/webapp-ingress --values /tmp/values-for-webapp-ingress.yaml

## uninstall
helm uninstall --namespace=city4people-wiki "${INSTANCE}-webapp-ingress"
```

### Шаги после создания сервера

#### Группа ssotelegram

<https://www.mediawiki.org/wiki/Manual:User_rights>

<https://city4people-wiki.ru/wiki/MediaWiki:Group-ssotelegram>

```txt
Пользователи Телеграма
```

<https://city4people-wiki.ru/wiki/MediaWiki:Group-ssotelegram-member>

```txt
пользователь Телеграма
```

<https://city4people-wiki.ru/wiki/MediaWiki:Grouppage-ssotelegram>

```txt
Пользователи Телеграма
```

#### Extensions

```sh
## (on the server)
MEDIAWIKI_PV_PATH=/var/www/local-pvs/city4people-wiki-main-mediawiki
EXTENSIONS_DIR=${MEDIAWIKI_PV_PATH}/mediawiki/extensions

## https://www.mediawiki.org/wiki/Extension:MobileFrontend
mv ${EXTENSIONS_DIR}/MobileFrontend ${EXTENSIONS_DIR}/MobileFrontend.bak
wget -c https://extdist.wmflabs.org/dist/extensions/MobileFrontend-REL1_35-8d06152.tar.gz -O - | tar -xz -C $EXTENSIONS_DIR

## https://www.mediawiki.org/wiki/Extension:TemplateStyles
mv ${EXTENSIONS_DIR}/TemplateStyles ${EXTENSIONS_DIR}/TemplateStyles.bak
wget -c https://extdist.wmflabs.org/dist/extensions/TemplateStyles-REL1_35-7743810.tar.gz -O - | tar -xz -C $EXTENSIONS_DIR

mv ${EXTENSIONS_DIR}/PluggableAuth ${EXTENSIONS_DIR}/PluggableAuth.bak
wget -c https://extdist.wmflabs.org/dist/extensions/PluggableAuth-REL1_35-2a465ae.tar.gz -O - | tar -xz -C $EXTENSIONS_DIR

chmod 755 ${EXTENSIONS_DIR}/Scribunto/includes/engines/LuaStandalone/binaries/lua5_1_5_linux_64_generic/lua

# rm -rf ${EXTENSIONS_DIR}/*.bak
```

##### Настройка плагина для авторизации через телеграм

```sh
# export TELEGRAM_BOT_TOKEN=
node --eval 'console.log(require("crypto").createHash("sha256").update(process.env.TELEGRAM_BOT_TOKEN).digest("hex"));'
```

### Skins

```sh
SKINS_DIR=${MEDIAWIKI_PV_PATH}/mediawiki/skins

# https://www.mediawiki.org/wiki/Skin:Minerva_Neue
mv ${SKINS_DIR}/MinervaNeue ${SKINS_DIR}/MinervaNeue.bak
wget -c https://extdist.wmflabs.org/dist/skins/MinervaNeue-REL1_35-bb52d27.tar.gz -O - | tar -xz -C $SKINS_DIR

# rm -rf ${SKINS_DIR}/*.bak
```

### Шаблоны

<https://city4people-wiki.ru/wiki/Служебная:Импорт>

Из [русской Википедии](https://ru.wikipedia.org/wiki/Служебная:Экспорт) (префикс интервики — `wikipedia_ru`):

```txt
MediaWiki:Common.css
MediaWiki:Mobile.css
MediaWiki:Minerva.css
Шаблон:Ambox
Шаблон:Внимание
```

- <https://www.mediawiki.org/wiki/Extension:Echo>
- <https://www.mediawiki.org/wiki/Extension:MobileFrontend>
- <https://www.mediawiki.org/wiki/Extension:StructuredDiscussions>
- <https://www.mediawiki.org/wiki/Extension:CirrusSearch>

Default user options: <https://www.mediawiki.org/wiki/Manual:$wgDefaultUserOptions>
`/opt/bitnami/mediawiki`

<https://city4people-wiki.ru/wiki/MediaWiki:Sitenotice>

```wiki
<div style="text-align:left">
{{Внимание|'''Сайт в процессе настройки и пока ещё не готов к наполнению'''<br/>

Экспериментальная вики [https://city4people.ru Горпроектов] создаётся для координации работы в Пензенском отделении. Если всё получится, область применения расширится до федерального уровня. Вики Горпроектов — частная инициатива. По любым вопросам пишите в телеграм [https://t.me/kachkaev @kachkaev]
}}
</div>
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

## Syncing

```sh
ssh remote mkdir -p /top/a/b/c/

watch rsync --archive --delete --stats --human-readable TelegramAuth/ kachkaev--firstvds--city4people-wiki:${MEDIAWIKI_PV_PATH}/mediawiki/extensions/TelegramAuth

watch rsync --archive --stats --human-readable LocalSettings*.php kachkaev--firstvds--city4people-wiki:${MEDIAWIKI_PV_PATH}/mediawiki/

watch rsync --archive --stats --human-readable visuals/main/mediawiki/*.png kachkaev--firstvds--city4people-wiki:${MEDIAWIKI_PV_PATH}/mediawiki/images/
```
