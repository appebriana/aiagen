@echo off
title AIAGEN MASTER STARTER
echo ==========================================
echo    MENYALAKAN SELURUH LAYANAN AIAGEN
echo ==========================================
echo.

:: Menjalankan Gateway Manager di jendela baru
echo [+] Menjalankan WhatsApp Gateway Manager...
start "AIAGEN Gateway Manager" cmd /c "run-gateways.bat"

:: Memberi jeda 3 detik
timeout /t 3 /nobreak > nul

:: Menjalankan Python AI Agent di jendela baru
echo [+] Menjalankan Python AI Agent...
start "AIAGEN Python AI Agent" cmd /c "run-ai.bat"

echo.
echo ==========================================
echo SEMUA LAYANAN SUDAH BERJALAN!
echo Silakan pantau jendela terminal masing-masing.
echo ==========================================
pause
