# subir.ps1 - Despliegue Rapido a Cualquier Repositorio de GitHub
param (
    [string]$Repo = "",
    [string]$AuthorName = "kronoxx404",
    [string]$AuthorEmail = "jadercastillo795@gmail.com"
)

Write-Host "==================================================" -ForegroundColor Yellow
Write-Host " SCRIPT DE DESPLIEGUE AUTOMATICO A GITHUB" -ForegroundColor Cyan
Write-Host "==================================================" -ForegroundColor Yellow

if ([string]::IsNullOrWhiteSpace($Repo)) {
    $Repo = Read-Host "Ingresa el nombre o URL del repo (ej: triconew o https://github.com/kronoxx404/triconew.git)"
}

if ([string]::IsNullOrWhiteSpace($Repo)) {
    Write-Host "Error: Debes ingresar un nombre o URL de repositorio." -ForegroundColor Red
    exit 1
}

# Normalizar URL del repositorio
if ($Repo -notmatch "^https?://") {
    $RepoName = $Repo.Replace(".git", "")
    $RepoUrl = "https://github.com/$AuthorName/$RepoName.git"
} else {
    $RepoUrl = $Repo
    if ($RepoUrl -match "/([^/]+?)(\.git)?$") {
        $RepoName = $matches[1]
    } else {
        $RepoName = "proyecto"
    }
}

Write-Host ""
Write-Host "1. Actualizando README.md con el nombre del repo (# $RepoName)..." -ForegroundColor Green
Set-Content -Path "README.md" -Value "# $RepoName" -Force

Write-Host "2. Configurando autor ($AuthorName <$AuthorEmail>)..." -ForegroundColor Green
git config user.name $AuthorName
git config user.email $AuthorEmail

Write-Host "3. Configurando remote origin -> $RepoUrl ..." -ForegroundColor Green
git remote remove origin 2>$null
git remote add origin $RepoUrl

Write-Host "4. Empaquetando y creando commit inicial limpio..." -ForegroundColor Green
$randomBranch = "deploy_" + (Get-Random)
git checkout --orphan $randomBranch 2>$null
git add -A
git commit --author="$AuthorName <$AuthorEmail>" -m "first commit"

git branch -D main 2>$null
git branch -m main

Write-Host "5. Subiendo a GitHub ($RepoUrl)..." -ForegroundColor Green
git push -u origin main --force

if ($LASTEXITCODE -eq 0) {
    Write-Host ""
    Write-Host "==================================================" -ForegroundColor Yellow
    Write-Host " [OK] PROYECTO SUBIDO EXITOSAMENTE A GITHUB!" -ForegroundColor Green
    Write-Host " Repositorio : $RepoUrl" -ForegroundColor Cyan
    Write-Host " Autor       : $AuthorName <$AuthorEmail>" -ForegroundColor Green
    Write-Host "==================================================" -ForegroundColor Yellow
} else {
    Write-Host ""
    Write-Host "[ERROR] Ocurrio un fallo durante el git push. Verifica la URL o permisos." -ForegroundColor Red
}
