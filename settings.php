<?php
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);

require 'vendor/autoload.php';

return parse_ini_file(dirname(__FILE__).'/settings.ini', true);