# Навык для Алисы - DeepSeek Chat

**Алиса DeepSeek** — навык для голосового ассистента Яндекс.Алиса, который интегрируется с DeepSeek Chat, обеспечивая интеллектуальные ответы на ваши вопросы.

## Установка

### Требования

- **PHP 7.4** или выше
- **Composer** для управления зависимостями
- **Расширение cURL** для PHP

### Шаги по установке

1. **Клонируйте репозиторий:**

    ```bash
    git clone https://github.com/zabarov/alice-deepseek.git .
    ```

2. **Установите зависимости с помощью Composer:**

    ```bash
    composer install
    ```

3. **Настройте файл окружения:**

    - Создайте файл `.env` на основе `.env.example`:

        ```bash
        cp .env.example .env
        ```

    - Откройте `.env` и добавьте ваш DeepSeek API ключ:

        ```
        DEEPSEEK_API_KEY=your_deepseek_api_key
        ```

4. **Настройте вебхук в навыке Алисы:**

	- Создайте навык https://dialogs.yandex.ru/developer/
    - Укажите публичный URL вашего сервера (например, `https://your-domain.com/index.php`) в настройках вебхука вашего навыка в Яндекс.Алисе.

## Ссылки

- DeepSeek API: https://api.deepseek.com
- Документация по API Алисы: https://yandex.ru/dev/dialogs/alice/doc/
- Руководство по разработке навыков для Алисы: https://yandex.ru/dev/dialogs/alice/doc/quickstart-about.html

При создании использовались данные с проекта https://github.com/peleccom/chat_gpt_yandex_alice

## Страница «Вместо Linear»

В `docs/` находится независимый статический каталог восьми альтернатив Linear: с поиском, фильтрами по типу инструмента и ссылками на официальные сайты. Страница на русском, адаптирована для мобильных устройств и работает без сборки, внешних шрифтов и библиотек. Без JavaScript каталог и ссылки остаются доступны.

Локальный просмотр:

```bash
python3 -m http.server 8080 --directory docs
```

Откройте http://localhost:8080. На существующем PHP-хостинге страница доступна по пути `/docs/`.

Для публикации на GitHub Pages выберите **Settings → Pages → Build and deployment → Source → GitHub Actions** в настройках репозитория. После слияния изменений в `main` workflow `.github/workflows/pages.yml` опубликует только содержимое `docs/`. Его также можно запустить вручную через **Actions → Publish alternatives to Linear → Run workflow**. Адрес после успешной публикации: https://ruslankarelin2.github.io/alice-deepseek/.
