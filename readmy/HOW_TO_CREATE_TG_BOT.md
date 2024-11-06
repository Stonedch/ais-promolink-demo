# Инструкция по созданию бота Telegram

- [Главная](/README.md)
- [Настройка](#настройка)

## Настройка

1. Переходим на https://t.me/BotFather
2. Регистрируемся
3. Получаем токен
4. Добавляем в /app/.env строку TELEGRAM_BOT_API_TOKEN="<token>" (см. в /app/.env.example)
5. Дергаем ссылку https://api.telegram.org/bot<token>/setwebhook?url=https://<AIS_URL>/api/telegram/handle
