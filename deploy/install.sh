#!/bin/bash

php bin/magento maintenance:enable
php bin/magento c:c
php bin/magento c:f
composer install
rm -rf pub/static/* var/cache/* var/composer_home/* var/page_cache/* var/view_preprocessed/* generated/code/*
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy -f
php bin/magento maintenance:disable
php bin/magento c:c
php bin/magento c:f