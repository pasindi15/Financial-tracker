@echo off
setlocal enabledelayedexpansion

REM Set Herd PHP paths
set HERD_BIN=C:\Program Files\Herd\resources\app.asar.unpacked\resources\bin
set HERD_PHP=C:\Program Files\Herd\resources\app.asar.unpacked\resources\php

REM Find PHP executable in Herd
for /f "delims=" %%A in ('dir "%HERD_BIN%\php*" /b 2^>nul ^| findstr /i "\.bat$" ^| findstr /v composer') do (
    set "FOUND_PHP=%%A"
    goto found
)

for /f "delims=" %%A in ('dir "%HERD_PHP%" /b 2^>nul ^| findstr /i "^php\.exe$"') do (
    set "FOUND_PHP=%%A"
    goto found
)

:found
if defined FOUND_PHP (
    echo Found PHP: !FOUND_PHP!
) else (
    echo PHP not found in Herd
)

REM Add to PATH
set PATH=!HERD_BIN!;!PATH!

REM Run composer if found
if exist "!HERD_BIN!\composer.bat" (
    call "!HERD_BIN!\composer.bat" %*
) else (
    echo Composer not found at !HERD_BIN!\composer.bat
)
