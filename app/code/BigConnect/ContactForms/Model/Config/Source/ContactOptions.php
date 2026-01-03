<?php

namespace BigConnect\ContactForms\Model\Config\Source;


class ContactOptions implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Initial options of available ContactOptions
     *
     * @return array
     */
    public function getOptions()
    {
        return [
            'disable' => __('Show nothing'),
            'phone'   => __('Show phone number'),
            'email'   => __('Show email adress'),
            ];
    }

    /**
     * Language options to use in Configuration
     *
     * @return array
     */
    public function toOptionArray()
    {
        $ContactOptions = [];
        foreach ($this->getOptions() as $value => $label) {
            $ContactOptions[] = ['value' => $value, 'label' => $label];
        }
        return $ContactOptions;
    }

}
