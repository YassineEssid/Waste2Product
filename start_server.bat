@echo off
echo ========================================
echo  Waste2Product - Laravel Server
echo ========================================
echo.
echo Creating cache directories...

REM Create cache directory if it doesn't exist
if not exist "bootstrap\cache" mkdir "bootstrap\cache"

REM Create empty cache files
echo ^<?php return []; > "bootstrap\cache\packages.php"
echo ^<?php return []; > "bootstrap\cache\services.php"

echo Cache files created successfully!
echo.
echo Starting Laravel development server...
echo Server will be available at: http://127.0.0.1:8000
echo.
echo Press Ctrl+C to stop the server
echo ========================================
echo.

REM Start PHP built-in server
php -S 127.0.0.1:8000 -t public
