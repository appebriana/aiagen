$f = "c:\laragon\www\aiagen\resources\views\layouts\pengaturan.blade.php"
$lines = Get-Content $f
$keep = $lines[0..147] + @('') + $lines[270..($lines.Length-1)]
Set-Content -Path $f -Value $keep -Encoding UTF8
