@echo off
REM Setup script to run Laravel commands with Herd

setlocal enabledelayedexpansion

set HERD_BIN=C:\Program Files\Herd\resources\app.asar.unpacked\resources\bin
set PROJECT_DIR=%~dp0
set PATH=!HERD_BIN!;!PATH!

cd /d "!PROJECT_DIR!"

if "%1"=="" (
    echo Financial Tracker - Laravel Setup Script
    echo.
    echo Usage:
    echo   setup.bat install       - Install dependencies
    echo   setup.bat migrate       - Run migrations
    echo   setup.bat seed          - Seed database
    echo   setup.bat serve         - Start dev server
    echo   setup.bat key           - Generate APP_KEY
    echo   setup.bat all           - Do all of the above
    echo.
    goto end
)

if "%1"=="install" (
    echo [1/4] Installing dependencies...
    call "!HERD_BIN!\composer.bat" install
    goto end
)

if "%1"=="key" (
    echo [2/4] Generating APP_KEY...
    call "!HERD_BIN!\php.bat" artisan key:generate
    goto end
)

if "%1"=="migrate" (
    echo [3/4] Running migrations...
    call "!HERD_BIN!\php.bat" artisan migrate
    goto end
)

if "%1"=="seed" (
    echo [4/4] Seeding database...
    call "!HERD_BIN!\php.bat" artisan db:seed
    goto end
)

if "%1"=="serve" (
    echo Starting development server...
    call "!HERD_BIN!\php.bat" artisan serve
    goto end
)

if "%1"=="all" (
    call :install
    call :key
    call :migrate
    call :seed
    echo.
    echo ✓ Setup complete! Run: setup.bat serve
    goto end
)

echo Unknown command: %1
goto end

:install
echo [1/4] Installing dependencies...
call "!HERD_BIN!\composer.bat" install
exit /b

:key
echo [2/4] Generating APP_KEY...
call "!HERD_BIN!\php.bat" artisan key:generate
exit /b

:migrate
echo [3/4] Running migrations...
call "!HERD_BIN!\php.bat" artisan migrate
exit /b

:seed
echo [4/4] Seeding database...
call "!HERD_BIN!\php.bat" artisan db:seed
exit /b

:end
endlocal
