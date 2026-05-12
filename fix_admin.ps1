$f = "c:\laragon\www\aiagen\resources\views\layouts\admin.blade.php"
$lines = Get-Content $f
$keep = $lines[0..141] + @('') + $lines[203..($lines.Length-1)]
Set-Content -Path $f -Value $keep -Encoding UTF8
