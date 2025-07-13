#!/bin/bash

echo "[INFO] .... Laravel Vite Vue Setup Script"

# Krok 1: Overenie požiadaviek
command -v php >/dev/null 2>&1 || { echo "[INFO] .... PHP nie je nainštalované."; exit 1; }
command -v composer >/dev/null 2>&1 || { echo "[INFO] .... Composer nie je nainštalovaný."; exit 1; }
command -v npm >/dev/null 2>&1 || { echo "[INFO] .... NPM nie je nainštalovaný."; exit 1; }

echo "[INFO] .... Požiadavky OK"

# Krok 2: Composer a NPM inštalácia
echo "[INFO] .... Inštalujem PHP balíky..."
composer install

echo "[INFO] .... Inštalujem JS balíky..."
npm install

# Krok 3: SQLite databáza
if [ ! -f database/database.sqlite ]; then
  echo "[INFO] .... Vytváram SQLite databázu..."
  mkdir -p database
  touch database/database.sqlite
else
  echo "[INFO] .... SQLite databáza už existuje."
fi

# Krok 4: Laravel migrácie (už ich aj spúšťaš nižšie, takže sa to dá preskočiť – ale nech ostane)
echo "[INFO] .... Spúšťam migrácie..."
php artisan migrate

# --- Tu pokračuje tvoja pôvodná logika ---

echo "[INFO] .... Pre spustenie aplikácie použi ./start.sh"
