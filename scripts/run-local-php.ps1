<#
Run the e-health-system using PHP built-in server for local dev.

Usage:
  Open PowerShell and run: .\scripts\run-local-php.ps1

Requirements: PHP CLI installed and Composer dependencies installed.
#>
Set-StrictMode -Version Latest

$projRoot = Split-Path -Parent $MyInvocation.MyCommand.Definition
Push-Location $projRoot

if (-not (Get-Command 'php' -ErrorAction SilentlyContinue)) {
    Write-Error "PHP CLI not found in PATH. Install PHP and try again."
    Pop-Location
    exit 1
}

if (-not (Test-Path "$projRoot\vendor\autoload.php")) {
    Write-Host "Composer dependencies not installed. Running 'composer install'..." -ForegroundColor Cyan
    if (-not (Get-Command 'composer' -ErrorAction SilentlyContinue)) {
        Write-Error "Composer not found. Install Composer (https://getcomposer.org/) and retry."
        Pop-Location
        exit 1
    }
    composer install
}

Write-Host "Starting PHP built-in server at http://localhost:8080" -ForegroundColor Cyan
Start-Process -NoNewWindow php -ArgumentList "-S localhost:8080 -t ."
Start-Sleep -Milliseconds 500
Start-Process "http://localhost:8080"

Pop-Location
