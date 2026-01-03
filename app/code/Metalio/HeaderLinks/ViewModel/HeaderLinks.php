<?php
declare(strict_types=1);

namespace Metalio\HeaderLinks\ViewModel;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Metalio\HeaderLinks\Model\Config;

class HeaderLinks implements ArgumentInterface
{
    public function __construct(
        private readonly Config $config,
        private readonly UrlInterface $url
    ) {}

    public function isEnabled(): bool
    {
        return $this->config->isEnabled();
    }

    /**
     * Admin môže vložiť:
     * - plnú URL: https://...
     * - absolútnu cestu: /vyroba-na-mieru
     * - alebo path/route: vyroba-na-mieru (preloží sa na store URL)
     *
     * @return array<int, array{label:string,href:string,sort:int,new_tab:bool,css:string}>
     */
    public function getLinks(): array
    {
        $links = $this->config->getLinks();

        foreach ($links as &$l) {
            $u = $l['url'];

            if (preg_match('#^https?://#i', $u) || str_starts_with($u, '/')) {
                $l['href'] = $u;
            } else {
                $l['href'] = $this->url->getUrl($u);
            }

            unset($l['url']); // už netreba
        }
        unset($l);

        return $links;
    }
}
