1) https://t.me/BotFather регистрируемся, получаем токен

2) добавляем в /app/.env строку TELEGRAM_BOT_API_TOKEN="<token>"

3) дергаем ссылку https://api.telegram.org/bot<token>/setwebhook?url=https://<AIS_URL>/api/telegram/handle