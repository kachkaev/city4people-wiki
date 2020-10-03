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

const loginMessage = `Откройте https://${botDomain} перейдите по ссылке «Войти» и нажмите кнопку «Войти через Телеграм».`;

bot.help((ctx) =>
  ctx.reply(
    `В режиме чата бот ничего полезного не делает. ${loginMessage}\n\nПо техническим вопросам пишите @kachkaev`,
  ),
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
