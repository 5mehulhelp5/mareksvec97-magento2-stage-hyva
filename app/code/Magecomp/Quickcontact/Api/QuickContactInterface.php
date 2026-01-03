<?php

namespace Magecomp\Quickcontact\Api;

interface QuickContactInterface
{
    /**
     * GET config data
     *
     * @return string
     * @param int $storeId
     */
    public function getConfigData($storeId);

    /**
     * SET form data
     * @param string $customername
     * @param string $customeremail
     * @param string $comment
     * @param int $storeId
     * @param string $image1
     * @param string $image1_name
     * @param string $image2
     * @param string $image2_name
     * @param string $image3
     * @param string $image3_name
     * @param string $image4
     * @param string $image4_name
     * @param string $image5
     * @param string $image5_name
     * @return string
     */
    public function setFormData($customername,$customeremail,$comment,$storeId,$image1,$image1_name,$image2,$image2_name,$image3,$image3_name,$image4,$image4_name,$image5,$image5_name);

}
