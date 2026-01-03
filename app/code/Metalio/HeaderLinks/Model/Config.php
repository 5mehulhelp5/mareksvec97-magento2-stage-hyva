<?php
declare(strict_types=1);

namespace Metalio\HeaderLinks\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    private const XML_ENABLED = 'metalio/header_links/enabled';
    private const XML_LINKS   = 'metalio/header_links/links';

    public function __construct(
        private ScopeConfigInterface $scopeConfig,
        private SerializerInterface $serializer
    ) {}

    public function isEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_ENABLED, ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getLinks(?int $storeId = null): array
    {
        $raw = $this->scopeConfig->getValue(self::XML_LINKS, ScopeInterface::SCOPE_STORE, $storeId);

        // ✅ DynamicRows sa zvyčajne uložia ako serializovaný string (často JSON)
        if (is_string($raw) && $raw !== '') {
            try {
                $raw = $this->serializer->unserialize($raw);
            } catch (\Throwable $e) {
                $raw = [];
            }
        }

        if (!is_array($raw)) {
            return [];
        }

        $links = [];
        foreach ($raw as $row) {
            $label = trim((string)($row['label'] ?? ''));
            $url   = trim((string)($row['url'] ?? ''));
            if ($label === '' || $url === '') continue;

            $links[] = [
                'label' => $label,
                'url' => $url,
                'sort' => (int)($row['sort'] ?? 0),
                'new_tab' => ((int)($row['new_tab'] ?? 0) === 1),
                'css' => trim((string)($row['css'] ?? '')),
            ];
        }

        usort($links, fn($a,$b) => $a['sort'] <=> $b['sort']);
        return $links;
    }
}
