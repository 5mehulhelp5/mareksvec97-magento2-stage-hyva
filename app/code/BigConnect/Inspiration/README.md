# BigConnect Inspiration

Admin správa:
- Content → Inspirations
- Grid umožňuje filtrovať status, store, product_id, country_code a upravovať záznamy.
- Formulár umožňuje upload fotky (uložené do `pub/media/inspirations/`) a napojenie na 1 produkt.

## Layout XML príklady (bez widgetov)

### A) CMS podstránka `inspiracie`
`app/design/frontend/<Vendor>/<theme>/Magento_Cms/layout/cms_page_view_selectable_inspiracie.xml`

```xml
<?xml version="1.0"?>
<page xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      xsi:noNamespaceSchemaLocation="urn:magento:framework:View/Layout/etc/page_configuration.xsd">
    <body>
        <referenceContainer name="content">
            <block class="BigConnect\Inspiration\Block\Gallery" name="bigconnect.inspiration.page" template="BigConnect_Inspiration::gallery.phtml">
                <arguments>
                    <argument name="context" xsi:type="string">page</argument>
                    <argument name="limit" xsi:type="number">60</argument>
                    <argument name="title" xsi:type="string">Inšpirácie</argument>
                    <argument name="show_header" xsi:type="boolean">true</argument>
                </arguments>
            </block>
        </referenceContainer>
    </body>
</page>
```

### B) Homepage
`app/design/frontend/<Vendor>/<theme>/Magento_Cms/layout/cms_index_index.xml`

```xml
<?xml version="1.0"?>
<page xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      xsi:noNamespaceSchemaLocation="urn:magento:framework:View/Layout/etc/page_configuration.xsd">
    <body>
        <referenceContainer name="content">
            <block class="BigConnect\Inspiration\Block\Gallery" name="bigconnect.inspiration.home" template="BigConnect_Inspiration::gallery.phtml">
                <arguments>
                    <argument name="context" xsi:type="string">homepage</argument>
                    <argument name="limit" xsi:type="number">8</argument>
                    <argument name="title" xsi:type="string">Inšpirácie</argument>
                    <argument name="show_header" xsi:type="boolean">true</argument>
                </arguments>
            </block>
        </referenceContainer>
    </body>
</page>
```

### C) PDP
`app/design/frontend/<Vendor>/<theme>/Magento_Catalog/layout/catalog_product_view.xml`

```xml
<?xml version="1.0"?>
<page xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      xsi:noNamespaceSchemaLocation="urn:magento:framework:View/Layout/etc/page_configuration.xsd">
    <body>
        <referenceContainer name="content">
            <block class="BigConnect\Inspiration\Block\Gallery" name="bigconnect.inspiration.product" template="BigConnect_Inspiration::gallery.phtml">
                <arguments>
                    <argument name="context" xsi:type="string">product</argument>
                    <argument name="limit" xsi:type="number">8</argument>
                    <argument name="title" xsi:type="string">Inšpirácie zákazníkov</argument>
                    <argument name="show_header" xsi:type="boolean">true</argument>
                </arguments>
            </block>
        </referenceContainer>
    </body>
</page>
```
