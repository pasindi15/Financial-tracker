# Financial Tracker - Setup Script for PowerShell
# This script uses Herd's bundled PHP & Composer

$HerdPath = "C:\Program Files\Herd\resources\app.asar.unpacked\resources\bin"
$ProjectPath = Split-Path -Parent $MyInvocation.MyCommand.Path
$PhpBat = "$HerdPath\php.bat"
$ComposerPhar = "$HerdPath\composer.phar"

if (-not (Test-Path $PhpBat)) {
    Write-Error "Cannot find php.bat at: $PhpBat"
    Write-Error "Make sure Laravel Herd is properly installed"
    exit 1
}

Set-Location $ProjectPath

Write-Host "=====================================" -ForegroundColor Cyan
Write-Host "Financial Tracker - Backend Setup" -ForegroundColor Cyan
Write-Host "=====================================" -ForegroundColor Cyan
Write-Host ""

# Step 1: Install dependencies
Write-Host "[1/4] Installing dependencies..." -ForegroundColor Yellow
& $PhpBat -r "require '$ComposerPhar';" install --no-interaction
if ($LASTEXITCODE -ne 0) {
    Write-Error "Failed to install dependencies"
    exit 1
}

# Step 2: Generate APP_KEY
Write-Host "[2/4] Generating APP_KEY..." -ForegroundColor Yellow
& $PhpBat artisan key:generate

# Step 3: Run migrations
Write-Host "[3/4] Running migrations..." -ForegroundColor Yellow
& $PhpBat artisan migrate --seed

Write-Host ""
Write-Host "=====================================" -ForegroundColor Green
Write-Host "✓ Setup Complete!" -ForegroundColor Green
Write-Host "=====================================" -ForegroundColor Green
Write-Host ""
Write-Host "Your API is ready! The backend is prepared." -ForegroundColor Green
Write-Host ""
Write-Host "To start the development server, run:" -ForegroundColor Cyan
Write-Host "  php artisan serve" -ForegroundColor White
Write-Host ""
