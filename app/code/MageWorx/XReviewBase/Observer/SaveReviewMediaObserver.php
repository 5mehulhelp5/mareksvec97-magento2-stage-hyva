<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\XReviewBase\Observer;

class SaveReviewMediaObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \MageWorx\XReviewBase\Model\ImageUploader
     */
    protected $imageUploader;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \MageWorx\XReviewBase\Model\Review\Media\Processor
     */
    protected $mediaProcessor;

    /**
     * SaveReviewMediaObserver constructor.
     *
     * @param \MageWorx\XReviewBase\Model\Review\Media\Processor $mediaProcessor
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \MageWorx\XReviewBase\Model\ImageUploader $imageUploader
     */
    public function __construct(
        \MageWorx\XReviewBase\Model\Review\Media\Processor $mediaProcessor,
        \Magento\Framework\App\RequestInterface $request,
        \MageWorx\XReviewBase\Model\ImageUploader $imageUploader
    ) {
        $this->imageUploader = $imageUploader;
        $this->request = $request;
        $this->mediaProcessor = $mediaProcessor;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Review\Model\Review $review */
        $review = $observer->getEvent()->getObject();
        $images = $this->getImages();

        if ($review && is_array($images) && count($images) > 0) {
            $mediaGalleryData = ['images' => []];

            $i = 0;
            foreach ($images as $files) {
                if (isset($files['tmp_name']) && strlen($files['tmp_name']) > 0) {
                    $result = $this->imageUploader->uploadImageToTmp($images[$i]);

                    if ($result['error']) {
                        throw new \Exception($result['error']);
                    }

                    if (!empty($result['file'])) {
                        $mediaGalleryData['images'][] = ['file' => $result['file']];
                    }
                }
                $i++;
            }

            if (!empty($mediaGalleryData['images'])) {
                $review->setData('media_gallery', $mediaGalleryData);
                $this->mediaProcessor->saveMedia($review);
            }
        }
    }

    /**
     * @return array
     */
    protected function getImages()
    {
        $images = [];

        foreach ($this->request->getFiles() as $key => $file) {
            if (strpos($key, 'customer-image-') === 0) {
                $images[] = $file;
            }
        }

        return $images;
    }
}
