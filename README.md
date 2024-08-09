# only

This is a simple application with registration, login and profile pages.

## Setup

### Cloning

```bash
git clone https://github.com/ryadovoyy/only.git
cd only
```

### Environment variables

Rename the env example file:

```bash
mv .env.example .env
```

Change `DB_USERNAME`, `DB_PASSWORD` and `DB_NAME` variables if you want. Paste your own client and server keys from the Yandex management console to `CAPTCHA_CLIENT_KEY` and `CAPTCHA_SECRET_KEY` variables respectively.

## Run

```bash
docker compose up
```

Then open `localhost:8080` in your browser.
