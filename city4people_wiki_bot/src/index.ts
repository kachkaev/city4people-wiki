import * as envalid from "envalid";
import { Telegraf } from "telegraf";

const {
  TELEGRAM_BOT_TOKEN: botToken,
  TELEGRAM_BOT_DOMAIN: botDomain,
} = envalid.cleanEnv(
  process.env,
  {
    TELEGRAM_BOT_TOKEN: envalid.str({}),
    TELEGRAM_BOT_DOMAIN: envalid.str({}),
  },
  { strict: true },
);

const bot = new Telegraf(botToken);

const loginMessage = `Зайдите на https://${botDomain}, нажмите кнопку «Войти через Телеграм», и вы получите сообщение от этого бота.`;

bot.start((ctx) =>
  ctx.reply(
    `Это бот экспериментальной вики Горпроектов. Он нужен для авторизации на сайте https://${botDomain}`,
  ),
);

bot.help((ctx) =>
  ctx.reply(`В режиме чата бот ничего полезного не делает. ${loginMessage}`),
);

bot.on("message", (ctx) => {
  ctx.reply(`Этот бот не умеет переписываться. ${loginMessage}`);
});

bot.on("connected_website", (ctx) => {
  ctx.reply("Сайт подключен");
  // eslint-disable-next-line no-console
  console.log(ctx);
});

bot.launch();
