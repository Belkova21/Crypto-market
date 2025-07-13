#!/bin/bash

# Štart viacerých procesov
php artisan migrate &
php artisan serve &
php artisan reverb:start &
php artisan queue:work &
php artisan schedule:run >> /dev/null 2>&1 &
php artisan schedule:run &
npm run dev
