<?php
namespace MetaloPro\ProductBlacklist\Logger;

use Monolog\Logger;
use MetaloPro\ProductBlacklist\Logger\Handler;

class BlacklistLogger extends Logger
{
    public function __construct(Handler $handler)
    {
        // Nastavenie názvu logera a priradenie handleru
        parent::__construct('blacklist', [$handler]);
    }
}
