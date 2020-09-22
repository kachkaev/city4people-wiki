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

bot.start((ctx) => ctx.reply("Welcome!"));
bot.help((ctx) => ctx.reply("Send me a sticker"));
bot.on("sticker", (ctx) => ctx.reply("👍"));
bot.hears("hi", (ctx) => ctx.reply("Hey there"));
bot.launch();
