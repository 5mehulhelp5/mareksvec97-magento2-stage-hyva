<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Magewire\Checkout;

use Hyva\Checkout\Model\Config\Source\TermsConditionsType;
use Hyva\Checkout\Model\ConfigData\HyvaThemes\Checkout as SystemCheckoutConfig;
use Hyva\Checkout\Model\Magewire\Component\EvaluationInterface;
use Hyva\Checkout\Model\Magewire\Component\EvaluationResultFactory;
use Hyva\Checkout\Model\Magewire\Component\EvaluationResultInterface;
use Magento\CheckoutAgreements\Api\CheckoutAgreementsListInterface;
use Magento\CheckoutAgreements\Api\Data\AgreementInterface;
use Magento\CheckoutAgreements\Model\AgreementFactory;
use Magento\CheckoutAgreements\Model\Api\SearchCriteria\ActiveStoreAgreementsFilter;
use Magento\Cms\Helper\Page;
use Magewirephp\Magewire\Component;

class TermsConditions extends Component implements EvaluationInterface
{
    public array $termAcceptance = [];

    protected CheckoutAgreementsListInterface $checkoutAgreementsList;
    protected ActiveStoreAgreementsFilter $activeStoreAgreementsFilter;
    protected SystemCheckoutConfig $systemCheckoutConfig;
    protected AgreementFactory $agreementFactory;
    protected Page $cmsPageHelper;

    protected array $uncallables = [
        'getAgreementsList'
    ];

    /* TBD: Magewire needs to accept 'termAcceptance.*' before this can be implemented */
    //protected $loader = ['termAcceptance' => 'Saving your preference'];

    public function __construct(
        CheckoutAgreementsListInterface $checkoutAgreementsList,
        ActiveStoreAgreementsFilter $activeStoreAgreementsFilter,
        SystemCheckoutConfig $systemCheckoutConfig,
        AgreementFactory $agreementFactory,
        Page $cmsPageHelper
    ) {
        $this->checkoutAgreementsList = $checkoutAgreementsList;
        $this->activeStoreAgreementsFilter = $activeStoreAgreementsFilter;
        $this->systemCheckoutConfig = $systemCheckoutConfig;
        $this->agreementFactory = $agreementFactory;
        $this->cmsPageHelper = $cmsPageHelper;
    }

    public function mount(): void
    {
        $this->termAcceptance = array_map(static function (AgreementInterface $agreement) {
            return !(bool)$agreement->getMode();
        }, $this->getAgreements());
    }

    /**
     * Temporary solution to show at least a loader for each term when it's enabled.
     *
     * @see $this->loader
     */
    public function getLoader(): array
    {
        $loader = [];

        /** @var AgreementInterface $agreement */
        foreach ($this->getAgreements() as $agreement) {
            $loader['termAcceptance.' . $agreement->getAgreementId()] = 'Saving your preference';
        }

        return $loader;
    }

    public function getAgreements(): array
    {
        if ($this->systemCheckoutConfig->getTermsAndConditionsType() === TermsConditionsType::TYPE_LIST) {
            return $this->checkoutAgreementsList->getList(
                $this->activeStoreAgreementsFilter->buildSearchCriteria()
            );
        }

        $term = $this->agreementFactory->create();

        $term->setContent($this->systemCheckoutConfig->getTermsAndConditionsMessage());
        $term->setData('includes_page', $this->systemCheckoutConfig->getTermsAndConditionsType() === TermsConditionsType::TYPE_PAGE);
        $term->setData('page_url', $this->cmsPageHelper->getPageUrl($this->systemCheckoutConfig->getTermsAndConditionsPage()) ?? null);

        return [
            $this->systemCheckoutConfig->getTermsAndConditionsType() => $term
        ];
    }

    public function getSystemConfig(): SystemCheckoutConfig
    {
        return $this->systemCheckoutConfig;
    }

    public function evaluateCompletion(EvaluationResultFactory $resultFactory): EvaluationResultInterface
    {
        $terms = array_filter($this->termAcceptance, static function ($value, $key) {
            return $value === false ? $key : false;
        }, ARRAY_FILTER_USE_BOTH);

        if (count($terms) !== 0) {
            /** @todo needs to migrate to "terms-conditions:details:error" eventually. */
            return $resultFactory->createErrorMessageEvent()
                ->withCustomEvent('terms-conditions:details:error')
                ->withMessage('Please accept all terms & conditions')
                ->withDetails([
                    'terms' => array_keys($terms)
                ]);
        }

        return $resultFactory->createSuccess([], 'terms-conditions:details:success');
    }
}
