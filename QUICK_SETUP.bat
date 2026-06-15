@echo off
setlocal enabledelayedexpansion

REM Direct execution using Herd's PHP binaries
REM No terminal GUI needed

set HERD_BIN=C:\Program Files\Herd\resources\app.asar.unpacked\resources\bin
set PROJECT=%~dp0

cd /d "!PROJECT!"

echo.
echo =====================================
echo Financial Tracker - Quick Setup
echo =====================================
echo.

REM We'll bypass composer and do manual setup since we have all files
echo [1/3] Generating APP_KEY...
call "!HERD_BIN!\php.bat" artisan key:generate
if !errorlevel! equ 0 (
    echo ✓ APP_KEY generated
) else (
    echo ✗ Failed to generate APP_KEY
    pause
    exit /b 1
)

echo [2/3] Creating database...
call "!HERD_BIN!\php.bat" artisan migrate --seed
if !errorlevel! equ 0 (
    echo ✓ Database created and seeded
) else (
    echo ✗ Failed to create database
    echo Try: php artisan migrate --seed
    pause
    exit /b 1
)

echo.
echo =====================================
echo ✓ Setup Complete!
echo =====================================
echo.
echo Your API is ready!
echo.
echo To start the server, run:
echo   php artisan serve
echo.
echo Then visit: http://localhost:8000/api/categories
echo.
pause
