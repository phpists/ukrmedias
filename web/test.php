<?php
error_reporting(-1);
ini_set('display_errors',true);
var_export(file_get_contents('https://asset-packagist.org/packages.json'));
