<?php
require 'app/bootstrap.php';

$bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $_SERVER);
$obj = $bootstrap->getObjectManager();

// Spustenie vášho custom cron jobu priamo
$cronJob = $obj->create(\MetaloPro\ProductBlacklist\Cron\BlacklistCheck::class);
$cronJob->execute();
