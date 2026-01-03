<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ReviewReminderBase\Model\Content\DataContainer;

use MageWorx\ReviewReminderBase\Model\Content\DataContainer;
use MageWorx\ReviewReminderBase\Model\Content\Converter\PopupContentConverter;

class PopupDataContainer extends DataContainer
{
    const CONTENT = 'content';

    /**
     * @var PopupContentConverter
     */
    protected $popupContentConverter;

    /**
     * PopupDataContainer constructor.
     *
     * @param PopupContentConverter $popupContentConverter
     */
    public function __construct(
        PopupContentConverter $popupContentConverter
    ) {
        $this->popupContentConverter = $popupContentConverter;
    }

    /**
     * @param string $content
     * @return PopupDataContainer
     */
    public function setContent(string $content): PopupDataContainer
    {
        return $this->setData(self::CONTENT, $content);
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->getData(self::CONTENT);
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getConvertedContent(): string
    {
        return $this->popupContentConverter->convert($this);
    }
}
