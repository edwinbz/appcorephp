@ECHO OFF

:choice
set /P c=Do you want to update AppCorePHP? (all files will be replaced) [Y/N]?
if /I "%c%" EQU "Y" goto :somewhere
if /I "%c%" EQU "N" goto :somewhere_else
goto :choice

:somewhere
curl -O https://raw.githubusercontent.com/edwinbz/appcorephp/master/commutator.php
CD ./src/app 
curl -O https://raw.githubusercontent.com/edwinbz/appcorephp/master/database.php
curl -O https://raw.githubusercontent.com/edwinbz/appcorephp/master/functions.php
curl -O https://raw.githubusercontent.com/edwinbz/appcorephp/master/main.php
curl -O https://raw.githubusercontent.com/edwinbz/appcorephp/master/modules.php
curl -O https://raw.githubusercontent.com/edwinbz/appcorephp/master/router.php

echo ¡AppCorePHP was successfully updated!
pause
exit

:somewhere_else
pause
exit