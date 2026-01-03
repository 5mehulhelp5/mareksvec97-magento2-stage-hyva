<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace  Magecomp\Quickcontact\Model\ResourceModel\Quickcontact;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	/**
	 * Define resource model
	 *
	 * @return void
	 */
	protected $_idFieldName = 'id';
	
	protected function _construct()
	{
		$this->_init('Magecomp\Quickcontact\Model\Quickcontact', 'Magecomp\Quickcontact\Model\ResourceModel\Quickcontact');
	}

}
