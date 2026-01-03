<?php
namespace MetaloPro\ProductBlacklist\Logger;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Handler extends StreamHandler
{
    public function __construct()
    {
        $filePath = BP . '/var/log/blacklist_check.log';
        parent::__construct($filePath, Logger::INFO);
    }
}
