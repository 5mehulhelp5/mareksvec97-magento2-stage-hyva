<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\XReviewBase\Plugin\Adminhtml;

class ReplaceReviewFormPlugin
{
    /**
     * @param \Magento\FrameWork\Code\NameBuilder $nameBuilder
     * @param string $result
     * @param string[] $parts
     * @return string
     */
    public function afterBuildClassName(\Magento\FrameWork\Code\NameBuilder $nameBuilder, $result, array $parts)
    {
        if ($result === 'Magento\Review\Block\Adminhtml\Edit\Form') {
            $result = 'MageWorx\XReviewBase\Block\Adminhtml\Edit\Form';
        }
        elseif ($result === 'Magento\Review\Block\Adminhtml\Add\Form') {
            $result = 'MageWorx\XReviewBase\Block\Adminhtml\Add\Form';
        }

        return $result;
    }
}
