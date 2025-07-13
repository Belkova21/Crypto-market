# Crypto Market App – Laravel + Vue + Vuetify

Tento projekt je postavený na Laravel (backend), Vue 3 + Vuetify (frontend), Tailwind CSS a využíva Laravel Reverb.

## Požiadavky

Pred spustením projektu sa uisti, že máš nainštalované:

- **PHP >= 8.2**
- **Composer**
- **Node.js >= 18**
- **NPM**
- **SQLite** alebo **MySQL** (podľa nastavenia `.env`)
- **Redis** (ak používaš broadcasting alebo queue)
- Laravel CLI (`php artisan`)

## Inštalácia a spustenie

1. **Príprava prostredia**

```bash
  ./setup.sh
```

2. **Nadstavenie .env**

- Vytvor .env podľa .env.example
- Vygeneruj si APP_KEY 
```bash
  php artisan key:generate
```
- Doplň hodnoty ako REVERB_APP_KEY, DB credentials a ďalšie podľa svojho prostredia

3. **Spusti aplikáciu**

```bash
  ./start.sh
```

## Prístup k aplikácii

Po spustení budeš mať aplikáciu dostupnú na: http://localhost:8000

