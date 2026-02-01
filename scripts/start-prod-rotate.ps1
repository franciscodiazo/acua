# Rotate log if bigger than 10 MB, then start production server with logs
# Usage: .\scripts\start-prod-rotate.ps1
$cwd = Split-Path -Parent $MyInvocation.MyCommand.Definition
$logPath = 'C:\temp\acua_server.log'
$maxBytes = 10MB

if (Test-Path $logPath) {
  $size = (Get-Item $logPath).Length
  if ($size -ge $maxBytes) {
    $ts = Get-Date -Format 'yyyyMMdd-HHmmss'
    $rotated = "C:\temp\acua_server-$ts.log"
    Rename-Item -Path $logPath -NewName (Split-Path $rotated -Leaf)
    Write-Output "Rotated log to $rotated"
  }
}

# Kill any process listening on port 3000
$port = 3000
$net = netstat -ano | Select-String ":$port"
if ($net) {
  $pid = ($net -split '\s+')[-1]
  try {
    Stop-Process -Id $pid -Force -ErrorAction Stop
    Write-Output "Stopped existing process on port $port (PID $pid)"
  } catch {
    Write-Output "Could not stop PID $pid: $_"
  }
}

# Start server redirecting output
Start-Process -FilePath cmd.exe -ArgumentList '/c','npm start > C:\temp\acua_server.log 2>&1' -WorkingDirectory $cwd -NoNewWindow -PassThru | Out-Null
Write-Output "Started production server with logs -> $logPath"