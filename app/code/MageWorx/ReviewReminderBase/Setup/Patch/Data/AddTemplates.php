<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ReviewReminderBase\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;
use MageWorx\ReviewReminderBase\Model\Reminder\Template as ReminderTemplate;

class AddTemplates implements DataPatchInterface, PatchVersionInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $templateTable = $this->moduleDataSetup->getTable('mageworx_reviewreminderbase_reminder_template');

        $select      = $this->moduleDataSetup->getConnection()->select()->from($templateTable, [ReminderTemplate::IDENTIFIER]);
        $identifiers = $this->moduleDataSetup->getConnection()->fetchCol($select);
        $templates   = $this->getTemplates();

        foreach ($templates as $template) {
            if (!in_array($template[ReminderTemplate::IDENTIFIER], $identifiers)) {
                $this->moduleDataSetup->getConnection()->insert($templateTable, $template);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function getVersion()
    {
        return '1.0.0';
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @return array
     */
    protected function getTemplates(): array
    {
        return [
            $this->getPopupTemplate(1, 'gift1', 'mwrv-modal--layout-3'),
            $this->getPopupTemplate(2, 'cart', 'mwrv-modal--layout-2 mwrv-modal--layout-2-color-1'),
            $this->getPopupTemplate(3, 'coupon', 'mwrv-modal--layout-2 mwrv-modal--layout-2-color-2'),
            $this->getPopupTemplate(4, 'gift2', 'mwrv-modal--layout-2 mwrv-modal--layout-2-color-3'),
            $this->getPopupTemplate(5, 'gift1', 'mwrv-modal--layout-2 mwrv-modal--layout-2-color-4'),
            $this->getPopupTemplate(6, 'gift1', '')
        ];
    }

    /**
     * @param int $id
     * @param string $imageCodeId
     * @param string $additionalСlass
     * @return array
     */
    protected function getPopupTemplate(int $id, string $imageCodeId, string $additionalСlass = ''): array
    {
        return [
            ReminderTemplate::IDENTIFIER => 'popup-' . $id,
            ReminderTemplate::TITLE      => '#' . $id,
            ReminderTemplate::CONTENT    => $this->getPopupContent($imageCodeId, $additionalСlass)
        ];
    }

    /**
     * @param string $imageCodeId
     * @param string $additionalСlass
     * @return string
     */
    protected function getPopupContent(string $imageCodeId, $additionalСlass = ''): string
    {
        $imageCode = $this->getImageCodeById($imageCodeId);

        return <<<EOD
<div class="mwrv-modal $additionalСlass mwrv-modal--open" tabindex="-1" role="dialog">
    <div class="mwrv-modal__dialog" role="document">
        <div class="mwrv-modal__content">
            <div class="mwrv-modal__header">
                <button class="mwrv-modal__close" type="button">
                    <svg width="18" height="18" viewBox="0 0 18 18" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17 1L1 17M1 1L17 17" stroke="currentColor" stroke-width="2"></path>
                    </svg>
                </button>
            </div>
            <div class="mwrv-modal__body">
                <h2 class="mwrv-modal__title">We want to hear YOU!</h2>
                <div class="mwrv-modal__banner">
                    <div class="mwrv-modal__banner-icon">
                        $imageCode
                    </div>
                    <div class="mwrv-modal__banner-content">Leave a review and receive up to <span
                        class="mwrv-modal__banner-content-highlighted">10%</span> off your next purchase
                    </div>
                </div>
                <h3 class="mwrv-modal__heading">Hi [customer.name]!</h3>
                <p class="mwrv-modal__text">I was indecisive on what size to get and I went through all of the questions
                    and reviews to see if anyone who was similar to me provided any helpful info.</p>
                <div class="mwrv__products">
                    <products>
                        <div class="mwrv__products-item">
                            <div class="mwrv__product">
                                <div class="mwrv__product-image"><img
                                    src="[product.image_url]" alt="[product.name]" target="_blank"/>
                                </div>
                                <div class="mwrv__product-content">
                                    <p class="mwrv-modal__text mwrv-modal__text--strong">[product.name]</p>
                                    <div class="mwrv-modal__rating">
                                        <div class="mwrv-modal__rating-value" style="width:[product.rating_summary]%"></div>
                                    </div>
                                    <a target="_blank" class="mwrv__link" href="[product.url]">Write a review</a>
                                </div>
                            </div>
                        </div>
                    </products>
                </div>
            </div>
        </div>
    </div>
</div>
EOD;
    }

    /**
     * @param string $id
     * @return string
     */
    protected function getImageCodeById(string $id): string
    {
        switch ($id) {
            case 'gift1':
                $imageCode = '<svg fill="none" height="56" viewBox="0 0 68 56" width="68" xmlns="http://www.w3.org/2000/svg"><g stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="m1 17 44-16 22 24v8l-44 16-22-24z"/><path d="m19 11 22 23.5v8"/><path d="m1 17 22 24m0 0 44-16m-44 16v8"/><path d="m55 12-44 16v8"/><path d="m4 28.5v8l17 18.5m0 0 44-16v-5m-44 21v-8"/><path d="m39.5 43v5"/><path d="m12 37v8"/><path d="m28.5964 22c2.8248-1.8761 6.6908-7.9734 2.6761-8.9114-3.7199-.8692-3.7169 4.8465-2.6761 8.9114z"/><path d="m27.7775 21.3762c-3.3154 1.7424-11.1114 2.2455-9.7273-2.152 1.2826-4.0746 6.5578-1.0251 9.7273 2.152z"/><path d="m26 31 2.5-9 12.5 2"/></g></svg>';
                break;
            case 'gift2':
                $imageCode = '<svg width="70" height="70" viewBox="0 0 51 57" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M40.881.545A1.5 1.5 0 0 1 42.145.8c4.444 3.333 6.682 9.886 5.576 15.968a1.5 1.5 0 0 1-.132.399l2.445.931A1.5 1.5 0 0 1 51 19.5v10a1.5 1.5 0 0 1-.948 1.395l-1.552.613V46a1.5 1.5 0 0 1-.796 1.324l-.125.06-21.5 9a1.5 1.5 0 0 1-1.017.05l-.141-.05-21.5-9a1.5 1.5 0 0 1-.915-1.246L2.5 46V31.51l-1.552-.614a1.5 1.5 0 0 1-.941-1.254L0 29.5v-10a1.5 1.5 0 0 1 .987-1.41l2.443-.888a1.5 1.5 0 0 1-.15-.434C2.172 10.686 4.41 4.133 8.854.8a1.5 1.5 0 0 1 1.264-.255c5.529 1.382 11.304 5.393 13.822 8.699a5.153 5.153 0 0 1 3.12-.002c2.516-3.304 8.291-7.315 13.82-8.697zM33.5 37.446l-7.448 2.949a1.5 1.5 0 0 1-1.104 0L17.5 37.446v12.579l7.999 3.348 8.001-3.349V37.446zm-28-4.75v12.306l7 2.93V35.467l-7-2.771zm40 0l-7 2.771v12.464l7-2.93V32.696zm-20-11.446L15.5 25v8.428l10 3.958 10-3.958V25l-10-3.75zm18.239-2.34c-1.972.457-4.475.832-6.903 1.023l-.441.033c-1.05.072-2.024.102-2.893.086L40.5 22.5v8.948L48 28.48v-7.947zm-36.325.035L3 20.55v7.93l7.5 2.968V22.5l6.999-2.448c-.99.019-2.115-.023-3.335-.12l-.38-.03c-2.245-.194-4.527-.54-6.37-.957zM25.5 12c-.69 0-1.436.36-2.057 1.02-.6.637-.943 1.42-.943 1.98 0 .964 1.218 2 3 2s3-1.036 3-2c0-.56-.343-1.343-.943-1.98-.62-.66-1.366-1.02-2.057-1.02zM10.103 3.65l-.124.11C7.73 5.81 6.295 9.08 6.04 12.534c4.777.222 9.436 1.66 13.008 3.513-.543-2.184.177-4.073 1.855-5.724l.019-.019-.242-.26c-2.313-2.424-6.492-5.18-10.55-6.386l-.027-.008zm30.793 0l-.446.139c-4.064 1.31-8.18 4.105-10.362 6.505l-.011.01.206.207c1.51 1.57 2.162 3.352 1.704 5.393 3.595-1.815 8.234-3.202 12.971-3.383-.257-3.448-1.69-6.713-3.937-8.76l-.125-.111zm-34.894 9.88L6 13.724c.003.5.03 1.002.085 1.502l.032.27.188.065c.258.086.566.174.916.263l4.185-1.522a29.853 29.853 0 0 0-5.404-.771zM45 13.52l-.078.002c-1.737.072-3.462.31-5.124.676l4.15 1.581c.359-.095.67-.188.919-.278l.015-.006.033-.27c.062-.567.09-1.137.085-1.705z"/></svg>';
                break;
            case 'cart':
                $imageCode = '<svg width="70" height="70" viewBox="0 0 59 59" xmlns="http://www.w3.org/2000/svg"><path d="M43 .5a1.5 1.5 0 0 1 0 3c-3.271 0-5.008 1.515-5.422 3.5H46.5a1.5 1.5 0 0 1 1.493 1.356L48 8.5V19h9.5a1.5 1.5 0 0 1 1.464 1.826l-.034.126-4.896 15.505a6.5 6.5 0 0 1-5.942 4.538l-.256.005H45.5a1.5 1.5 0 0 1-.144-2.993L45.5 38h2.336a3.5 3.5 0 0 0 3.272-2.259l.065-.187L55.453 22H15.616l1.73 16H36a1.5 1.5 0 0 1 .144 2.993L36 41H15a2.5 2.5 0 0 0-.164 4.995L15 46h32.5a1.5 1.5 0 0 1 .45.069A6.502 6.502 0 0 1 47 59a6.5 6.5 0 0 1-5.478-10H28.478a6.5 6.5 0 1 1-10.956 0H15a5.5 5.5 0 0 1-.667-10.96L10.991 7.124a3.5 3.5 0 0 0-3.29-3.119L7.512 4H2a1.5 1.5 0 0 1-.144-2.993L2 1h5.511a6.5 6.5 0 0 1 6.433 5.567l.03.234L15.292 19H24V8.5a1.5 1.5 0 0 1 1.356-1.493L25.5 7h8.91c-.263-.855-1.017-1.286-2.622-1.515a1.5 1.5 0 0 1 .28-2.984l.144.014c1.495.214 2.654.663 3.503 1.322C37.065 1.854 39.512.5 43 .5zM47 49a3.5 3.5 0 1 0 0 7 3.5 3.5 0 0 0 0-7zm-24 0a3.5 3.5 0 1 0 0 7 3.5 3.5 0 0 0 0-7zm11.5-39H27v9h7.5v-9zM45 10h-7.5v9H45v-9z"/></svg>';
                break;
            case 'coupon':
                $imageCode = '<svg width="70" height="70" viewBox="0 0 61 32" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M11.586 0a2.5 2.5 0 0 1 1.768.732L15 2.38 16.646.732a2.5 2.5 0 0 1 1.57-.724L18.414 0H58.5A2.5 2.5 0 0 1 61 2.5v27a2.5 2.5 0 0 1-2.5 2.5H18.414a2.5 2.5 0 0 1-1.768-.732L15 29.62l-1.646 1.647a2.5 2.5 0 0 1-1.57.724l-.198.008H2.5A2.5 2.5 0 0 1 0 29.5v-27A2.5 2.5 0 0 1 2.5 0zm-.207 3H3v26h8.378l1.854-1.854a2.5 2.5 0 0 1 3.405-.121l.13.121L18.622 29H58V3H18.62l-1.852 1.854a2.5 2.5 0 0 1-3.405.121l-.13-.121L11.378 3zM43 17a4 4 0 1 1 0 8 4 4 0 0 1 0-8zm3.707-9.707a1 1 0 0 1 .083 1.32l-.083.094-16 16a1 1 0 0 1-1.497-1.32l.083-.094 16-16a1 1 0 0 1 1.414 0zM15 21a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm28-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-28-4a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm18-8a4 4 0 1 1 0 8 4 4 0 0 1 0-8zm0 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM15 9a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/></svg>';
                break;
            default:
                $imageCode = '';
                break;
        }

        return $imageCode;
    }
}
