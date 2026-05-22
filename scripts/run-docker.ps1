<#
Run the e-health-system using Docker Compose.

Usage: Open PowerShell as Administrator (or normal shell) and run:
  .\scripts\run-docker.ps1

Requirements: Docker Desktop installed and running, project .env (optional).
#>
Set-StrictMode -Version Latest

$projRoot = Split-Path -Parent $MyInvocation.MyCommand.Definition
Push-Location $projRoot

Write-Host "Starting e-health-system via Docker Compose..." -ForegroundColor Cyan

if (-not (Get-Command 'docker' -ErrorAction SilentlyContinue)) {
    Write-Error "Docker is not installed or not in PATH. Install Docker Desktop and try again."
    Pop-Location
    exit 1
}

# Build and start services
docker compose up --build -d

Write-Host "Waiting for database to become available..." -ForegroundColor Cyan
$maxAttempts = 30
$attempt = 0
while ($attempt -lt $maxAttempts) {
    try {
        docker compose exec -T db mysql -uroot -p"secret" -e "SELECT 1;" | Out-Null
        if ($LASTEXITCODE -eq 0) { break }
    } catch {
        # ignore
    }
    Start-Sleep -Seconds 2
    $attempt++
}

if ($attempt -ge $maxAttempts) {
    Write-Warning "Database did not become available in time. Check Docker containers with 'docker compose ps'."
} else {
    Write-Host "Importing database schema (database.sql) if present..." -ForegroundColor Green
    if (Test-Path "$projRoot\database.sql") {
        Get-Content "$projRoot\database.sql" -Raw | docker compose exec -T db sh -c 'exec mysql -u root -p"'$env:MYSQL_ROOT_PASSWORD'" e_health'  
        Write-Host "Database import attempted." -ForegroundColor Green
    } else {
        Write-Host "No database.sql found; skipping import." -ForegroundColor Yellow
    }
}

Write-Host "Opening web UI at http://localhost:8080" -ForegroundColor Cyan
Start-Process "http://localhost:8080"

Pop-Location
