$ErrorActionPreference = 'Stop'

$projectRoot = Split-Path -Parent $MyInvocation.MyCommand.Path
$vendorRoot = Join-Path $projectRoot 'assets\vendor'

$items = @(
    @{ Url = 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css'; Out = 'bootstrap\bootstrap.min.css' },
    @{ Url = 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js'; Out = 'bootstrap\bootstrap.bundle.min.js' },

    @{ Url = 'https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css'; Out = 'fontawesome\css\all.min.css' },
    @{ Url = 'https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/webfonts/fa-brands-400.woff2'; Out = 'fontawesome\webfonts\fa-brands-400.woff2' },
    @{ Url = 'https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/webfonts/fa-regular-400.woff2'; Out = 'fontawesome\webfonts\fa-regular-400.woff2' },
    @{ Url = 'https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/webfonts/fa-solid-900.woff2'; Out = 'fontawesome\webfonts\fa-solid-900.woff2' },
    @{ Url = 'https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/webfonts/fa-v4compatibility.woff2'; Out = 'fontawesome\webfonts\fa-v4compatibility.woff2' },

    @{ Url = 'https://code.jquery.com/jquery-3.7.1.min.js'; Out = 'jquery\jquery-3.7.1.min.js' },

    @{ Url = 'https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css'; Out = 'datatables\dataTables.bootstrap5.min.css' },
    @{ Url = 'https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css'; Out = 'datatables\responsive.bootstrap5.min.css' },
    @{ Url = 'https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js'; Out = 'datatables\jquery.dataTables.min.js' },
    @{ Url = 'https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js'; Out = 'datatables\dataTables.bootstrap5.min.js' },
    @{ Url = 'https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js'; Out = 'datatables\dataTables.responsive.min.js' },
    @{ Url = 'https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js'; Out = 'datatables\responsive.bootstrap5.min.js' },

    @{ Url = 'https://cdn.jsdelivr.net/npm/chart.js/dist/chart.umd.min.js'; Out = 'chartjs\chart.umd.min.js' },

    @{ Url = 'https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js'; Out = 'jspdf\jspdf.umd.min.js' },
    @{ Url = 'https://cdn.jsdelivr.net/npm/jspdf-autotable@3.8.4/dist/jspdf.plugin.autotable.min.js'; Out = 'jspdf-autotable\jspdf.plugin.autotable.min.js' },

    @{ Url = 'https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js'; Out = 'xlsx\xlsx.full.min.js' }
)

function Ensure-DirectoryForFile([string]$filePath) {
    $dir = Split-Path -Parent $filePath
    if (-not (Test-Path $dir)) {
        New-Item -ItemType Directory -Path $dir -Force | Out-Null
    }
}

Write-Host "Downloading vendor assets to: $vendorRoot" -ForegroundColor Cyan

foreach ($it in $items) {
    $outPath = Join-Path $vendorRoot $it.Out
    Ensure-DirectoryForFile $outPath

    Write-Host "- $($it.Url) -> $outPath" -ForegroundColor Gray
    Invoke-WebRequest -Uri $it.Url -OutFile $outPath -UseBasicParsing
}

Write-Host "Done. You can now run the app offline (templates prefer local assets/vendor/* when files exist)." -ForegroundColor Green
