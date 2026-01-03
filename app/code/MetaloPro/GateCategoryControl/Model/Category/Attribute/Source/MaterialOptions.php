<?php
namespace MetaloPro\GateCategoryControl\Model\Category\Attribute\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Framework\App\ObjectManager;

class MaterialOptions extends AbstractSource
{
    protected $eavConfig;

    public function __construct(EavConfig $eavConfig = null)
    {
        $this->eavConfig = $eavConfig ?: ObjectManager::getInstance()->get(EavConfig::class);
    }

    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options[] = ['label' => __('-- None --'), 'value' => ''];

            try {
                $attribute = $this->eavConfig->getAttribute(\Magento\Catalog\Model\Product::ENTITY, 'material');


                if ($attribute && $attribute->usesSource()) {
                    $options = $attribute->getSource()->getAllOptions(false);
                    foreach ($options as $option) {
                        $this->_options[] = [
                            'label' => $option['label'],
                            'value' => $option['value']
                        ];
                    }
                }
            } catch (\Exception $e) {
                // Log or silently fail
            }
        }

        return $this->_options;
    }
}
