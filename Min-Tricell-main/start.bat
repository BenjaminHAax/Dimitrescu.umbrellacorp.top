@echo off
title Node Server - Running

:loop
echo [%date% %time%] Starting node index.js...
node index.js
echo [%date% %time%] Process exited. Restarting in 2 seconds...
timeout /t 2 /nobreak >nul
goto loop
