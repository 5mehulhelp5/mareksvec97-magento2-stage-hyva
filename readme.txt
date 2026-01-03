# Steelbro Magento 2

Projektová inštalácia Magento Open Source 2.4.8 s balíkmi Hyvä/Breeze a rozšíreniami tretích strán.

## Požiadavky
- PHP 8.2+ s rozšíreniami odporúčanými Magento
- Composer 2.x
- MySQL/MariaDB databáza
- Redis pre cache a session
- OpenSearch/Elasticsearch pre fulltext

## Rýchly štart
1. Nainštaluj závislosti: `composer install`.
2. Vytvor `app/etc/env.php` s prístupmi k databáze, cache a vyhľadávaniu (možno použiť šablónu prostredia ak je dostupná).
3. Spusť migrácie a registráciu modulov: `bin/magento setup:upgrade`.
4. Pre vývoj nechaj obchod v developer móde: `bin/magento deploy:mode:set developer`.
5. Pre produkciu skompiluj kód a statický obsah: 
   - `bin/magento setup:di:compile`
   - `bin/magento setup:static-content:deploy sk_SK en_US`
6. Vymaž cache po zmenách: `bin/magento cache:flush`.

## Užitočné príkazy
- Reindexácia: `bin/magento indexer:reindex`
- Vytvorenie administračného používateľa: `bin/magento admin:user:create`
- Kontrola stavu modulov: `bin/magento module:status`

## Tipy pre vývoj
- Modulový kód nájdeš v `app/code`, generovaný kód v `generated/code`.
- Konfigurácie prostredí udržiavaj v `app/etc/env.php` a nasadzuj bezpečne mimo repozitár.
- Po úpravách frontendu vždy nasadzuj statické súbory pre všetky používané jazykové mutácie.



7. Zmeny 
Oprava stripe: composer require hyva-themes/magento2-hyva-checkout-stripe