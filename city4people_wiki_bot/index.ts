import * as envalid from "envalid";
import { Telegraf } from "telegraf";

const { BOT_TOKEN } = envalid.cleanEnv(
  process.env,
  {
    BOT_TOKEN: envalid.str({}),
  },
  { strict: true },
);

const bot = new Telegraf(BOT_TOKEN);

const loginMessage =
  "Зайдите на https://city4people-wiki.ru, нажмите кнопку «Войти через Телеграм», и вы получите сообщение от этого бота.";

bot.start((ctx) =>
  ctx.reply(
    "Это бот экспериментальной вики Горпроектов. Он нужен для авторизации на сайте https://city4people-wiki.ru",
  ),
);

bot.help((ctx) =>
  ctx.reply(`Сам по себе бот ничего не делает. ${loginMessage}`),
);

bot.on("message", (ctx) => {
  ctx.reply(`Этот бот не умеет переписываться. ${loginMessage}`);
});

bot.launch();
