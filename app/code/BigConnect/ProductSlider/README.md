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

## Použitie cez layout XML

Slider sa vkladá cez layout XML v téme. Príklad vloženia na homepage (`cms_index_index.xml`):

```xml
<referenceContainer name="content">
    <block class="BigConnect\ProductSlider\Block\ProductSlider"
           name="homepage.product.slider"
           template="BigConnect_ProductSlider::widget/product-slider.phtml">
        <arguments>
            <argument name="source_code" xsi:type="string">bestsellers</argument>
            <argument name="page_size" xsi:type="number">24</argument>
            <argument name="period_days" xsi:type="number">360</argument>
            <argument name="preset" xsi:type="string">item1</argument>
        </arguments>
    </block>
</referenceContainer>
```

Titulky, CTA a farby sa nastavujú v `Stores → Configuration → Hyva Starter Settings → Product Slider settings` podľa zvoleného presetu.

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
3. Nie je potrebné meniť template — slider automaticky použije nový zdroj.

## Poznámky

- Ak reporty bestsellerov neobsahujú dáta, slider nevykreslí nič.
- Modul je pripravený na doplnenie zdrojov ako New, Sale, MostViewed, Attribute, SKU list, Rule, Category, atď.
