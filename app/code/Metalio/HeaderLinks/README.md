# Metalio_HeaderLinks

Pridá nastaviteľné custom linky do headeru (pravá strana) cez Magento konfiguráciu.

## Inštalácia
1) Skopíruj modul do:
   app/code/Metalio/HeaderLinks

2) Spusť:
   bin/magento module:enable Metalio_HeaderLinks
   bin/magento setup:upgrade
   bin/magento cache:flush

## Nastavenie v admine
Stores → Configuration → General → Metalio → Header Links

- Enable: Yes/No
- Custom links: pridaj riadky (Label, URL/Path, Sort, New tab, CSS class)

## Výpis v Hyvä headeri
Tento modul pridáva len template a viewmodel. Do headeru si ho vlož cez layout alebo priamo do šablóny:

### Varianta A: vloženie do šablóny headeru
<?= $block->getChildHtml('metalio.header.links') ?>

a následne cez layout zaregistruj block, ktorý používa template:
Metalio_HeaderLinks::header/links.phtml

### Varianta B: layout
Vytvor/override layout a vlož block na miesto v headeri podľa tvojej témy.
