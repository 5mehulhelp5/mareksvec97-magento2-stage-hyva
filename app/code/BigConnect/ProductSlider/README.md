# BigConnect Product Slider

Magento 2 modul pre univerzálny produktový slider s rozšíriteľnými zdrojmi produktov.
Momentálne je implementovaný zdroj **Bestsellers** (podľa predajov za zvolené obdobie).

## Inštalácia

1. Nahrajte modul do `app/code/BigConnect/ProductSlider`.
2. Spustite:
   ```bash
   bin/magento module:enable BigConnect_ProductSlider
   bin/magento setup:upgrade
   ```

## Použitie widgetu

1. V administrácii otvorte CMS stránku alebo blok.
2. Vložte widget **BigConnect: Product Slider**.
3. Nastavte požadované parametre (zdroj, titulky, počet produktov, obdobie atď.).

Príklad vloženia do CMS obsahu:

```text
{{widget type="BigConnect\ProductSlider\Block\Widget\ProductSlider" source_code="bestsellers" page_size="8" period_days="30" only_in_stock="1" only_visible="1"}}
```

## Ako pridať nový source

1. Vytvorte triedu implementujúcu `BigConnect\ProductSlider\Api\ProductSourceInterface`.
2. Zaregistrujte nový zdroj v `etc/di.xml` do `BigConnect\ProductSlider\Model\Source\SourcePool`:
   ```xml
   <type name="BigConnect\ProductSlider\Model\Source\SourcePool">
       <arguments>
           <argument name="sources" xsi:type="array">
               <item name="new" xsi:type="object">Vendor\Module\Model\Source\NewSource</item>
           </argument>
       </arguments>
   </type>
   ```
3. Pridajte nový option do `etc/widget.xml` v parametri `source_code`.
4. Nie je potrebné meniť template — slider automaticky použije nový zdroj.

## Poznámky

- Ak reporty bestsellerov neobsahujú dáta, widget nevykreslí nič.
- Modul je pripravený na doplnenie zdrojov ako New, Sale, MostViewed, Attribute, SKU list, Rule, Category, atď.
