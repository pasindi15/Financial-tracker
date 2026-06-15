@echo off
REM Financial Tracker - Automated Setup Script
REM This script uses Herd's PHP/Composer with proper environment

setlocal enabledelayedexpansion

REM Define Herd paths
set HERD_PATH=C:\Program Files\Herd\resources\app.asar.unpacked\resources
set HERD_BIN=!HERD_PATH!\bin
set PROJECT_DIR=%~dp0

REM Change to project directory
cd /d "!PROJECT_DIR!"

echo.
echo ====================================
echo Financial Tracker - Backend Setup
echo ====================================
echo.

REM Check if we can find composer
if not exist "!HERD_BIN!\composer.bat" (
    echo ERROR: Cannot find composer.bat at !HERD_BIN!\composer.bat
    echo.
    echo Please ensure Laravel Herd is properly installed.
    pause
    exit /b 1
)

REM Try to install using Herd's php directly
echo [1/4] Installing dependencies...
call "!HERD_BIN!\php.bat" -d register_argc_argv=On "!HERD_BIN!\composer.phar" install --no-interaction
if !errorlevel! neq 0 (
    echo ERROR: Failed to install dependencies
    pause
    exit /b 1
)

echo [2/4] Generating APP_KEY...
call "!HERD_BIN!\php.bat" artisan key:generate

echo [3/4] Running migrations...
call "!HERD_BIN!\php.bat" artisan migrate --seed

echo.
echo ====================================
echo ✓ Setup Complete!
echo ====================================
echo.
echo To start the server, run:
echo   php artisan serve
echo.
echo Or use Herd's Terminal option for your site
echo.
pause
