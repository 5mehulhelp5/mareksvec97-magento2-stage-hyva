<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Magewire\Component\Evaluation;

use Hyva\Checkout\Model\ConfigData\HyvaThemes\Developer\SystemConfigEvaluationApi;
use Hyva\Checkout\Model\Magewire\Component\Evaluation\Concern\DispatchCapabilities;
use Hyva\Checkout\Model\Magewire\Component\Evaluation\Concern\StackingCapabilities;
use Laminas\Uri\UriFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Url\HostChecker;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;
use Magewirephp\Magewire\Component;

class Redirect extends EvaluationResult
{
    use DispatchCapabilities;
    use StackingCapabilities;

    public const TYPE = 'redirect';

    public const URL_TYPE_INTERNAL = 'internal';
    public const URL_TYPE_EXTERNAL = 'external';

    private HostChecker $hostChecker;
    private ScopeConfigInterface $scopeConfig;
    private UrlInterface $urlBuilder;
    private SystemConfigEvaluationApi $systemConfigEvaluationApi;

    private string $url;
    private int $timeout = 0;
    private bool $confirmation = false;
    private bool $notification = false;
    private ?string $dialogMessage = null;
    private ?string $dialogTitle = null;
    private array $actions = [];

    public function __construct(
        HostChecker $hostChecker,
        ScopeConfigInterface $scopeConfig,
        UrlInterface $urlBuilder,
        SystemConfigEvaluationApi $systemConfigEvaluationApi,
        string $url
    ) {
        $this->hostChecker = $hostChecker;
        $this->scopeConfig = $scopeConfig;
        $this->urlBuilder = $urlBuilder;
        $this->systemConfigEvaluationApi = $systemConfigEvaluationApi;

        $this->url = $url;
        $this->stackPosition = 10900;
    }

    /**
     * Set the redirect url.
     */
    public function withUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Set a redirect timeout.
     */
    public function withTimeout(int $milliseconds): self
    {
        $this->timeout = $milliseconds;

        return $this;
    }

    /**
     * Enables a confirmation dialog.
     */
    public function withConfirmationDialog(?string $message = null): self
    {
        $this->confirmation = true;

        if ($message) {
            $this->withDialogMessage($message);
        }

        return $this;
    }

    /**
     * Enables a notification dialog.
     */
    public function withNotificationDialog(?string $message = null): self
    {
        $this->notification = true;

        if ($message) {
            $this->withDialogMessage($message);
        }

        return $this;
    }

    /**
     * Set a message.
     */
    public function withDialogMessage(string $message): self
    {
        $this->dialogMessage = $message;

        return $this;
    }

    /**
     * Set a title.
     */
    public function withDialogTitle(string $title): self
    {
        $this->dialogTitle = $title;

        return $this;
    }

    public function getArguments(Component $component): array
    {
        $url = $this->generateUrl();

        $arguments = [
            'url' => $url->toString(),
            'url_type' => $this->isExternalUrl($url->toString()) ? 'external' : 'internal',
            'url_secure' => $url->getScheme() === 'https',
            'timeout' => $this->timeout,
            'details' => false,
            'actions' => []
        ];

        /*
         * Confirmation dialog requirements:
         *
         * 1. When the URL is insecure.
         * 2. When the URL is external and insecure.
         * 3. When the URL is external and the dialog is enabled or forced.
         */
        if ($arguments['url_type'] === 'external' && (! $arguments['url_secure'] || $this->canShowConfirmationDialog())) {
            $arguments['details'] = [
                'title' => __($this->dialogTitle ?? 'Redirect'),
                'message' => __($this->getConfirmationMessage()),
                'actions' => [
                    'decline' => __('Decline'),
                    'confirm' => __('Continue'),
                ],
            ];
        } elseif (($this->forceNotificationDialog() && $arguments['url_type'] === 'external') || $this->canShowNotificationDialog()) {
            $arguments['details'] = [
                'title' => $this->dialogTitle ? __($this->dialogTitle) : '',
                'message' => __($this->getNotificationMessage())
            ];

            $arguments['timeout'] = $this->timeout ?? $this->getDialogVisibilityDuration();
        }

        return $arguments;
    }

    private function generateUrl(): \Laminas\Uri\Uri
    {
        $url = $this->url ?? '/';

        if (strncmp('www.', $url, 4) === 0) {
            $url = str_replace('www.', '', $url);
        }

        $parse = UriFactory::factory($url);

        if ($this->hostChecker->isOwnOrigin($parse->toString())) {
            if ($url === '/') {
                $url = $this->scopeConfig->getValue('web/default/front', ScopeInterface::SCOPE_STORE);
            }

            return UriFactory::factory(
                $this->urlBuilder->getUrl($url === '/' ? $url : ltrim($url, '\/'))
            );
        }

        return $parse;
    }

    private function isExternalUrl(string $url): bool
    {
        if (strncmp('www.', $url, 4) === 0) {
            $url = str_replace('www.', '', $url);
        }

        return ! $this->hostChecker->isOwnOrigin(UriFactory::factory($url)->toString());
    }

    private function getConfirmationMessage(): string
    {
        return $this->dialogMessage ?? $this->systemConfigEvaluationApi->getConfirmationMessage();
    }

    private function getNotificationMessage(): string
    {
        return $this->dialogMessage ?? $this->systemConfigEvaluationApi->getNotificationMessage();
    }

    private function getDialogVisibilityDuration(): int
    {
        return $this->timeout ?? $this->systemConfigEvaluationApi->getVisibilityDuration();
    }

    private function canShowConfirmationDialog(): bool
    {
        if ($this->confirmation && $this->systemConfigEvaluationApi->canShowConfirmationDialog()) {
            return true;
        } elseif ($this->systemConfigEvaluationApi->canForceConfirmationDialog()) {
            return true;
        }

        return false;
    }

    private function canShowNotificationDialog(): bool
    {
        if ($this->notification && $this->systemConfigEvaluationApi->canShowNotificationDialog()) {
            return true;
        } elseif ($this->forceNotificationDialog()) {
            return true;
        }

        return false;
    }

    private function forceNotificationDialog(): bool
    {
        return $this->systemConfigEvaluationApi->canForceNotificationDialog();
    }
}
