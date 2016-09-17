@echo off
REM This file is used for executing synful management commands
REM on windows machines. 

if not exist "./src/Synful/Synful.php" (
    echo Must be run from Synful root directory.
) else (
    if not "%1"=="" (
        if "%1"=="install" (
            composer install --no-scripts
        ) else goto run
    ) else (
        :run
        cd public
        php index.php %*
        cd ..
    )
)
