<?php
namespace N1site\Menu\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\Data\Tree\Node\Collection;
use Magento\Framework\Data\Tree\NodeFactory;
use Magento\Framework\Data\TreeFactory;
use Magento\Catalog\Model\CategoryFactory;

class Topmenu extends \Magento\Theme\Block\Html\Topmenu {
	

    // private $nodeFactory;
    // private $treeFactory;
	
	protected $categoryFactory;
	
    public function __construct(
        Template\Context $context,
        NodeFactory $nodeFactory,
        TreeFactory $treeFactory,
		CategoryFactory $categoryFactory,
        array $data = []
    ) {
        parent::__construct($context, $nodeFactory, $treeFactory, $data);
        $this->nodeFactory = $nodeFactory;
        $this->treeFactory = $treeFactory;
		$this->categoryFactory = $categoryFactory;
    }
	
    protected function _getHtml(
        Node $menuTree,
        $childrenWrapClass,
        $limit,
        array $colBrakes = []
    ) {
        $html = '';

        $children = $menuTree->getChildren();
        $childLevel = $this->getChildLevel($menuTree->getLevel());
        $this->removeChildrenWithoutActiveParent($children, $childLevel);

        $counter = 1;
        $childrenCount = $children->count();

        $parentPositionClass = $menuTree->getPositionClass();
        $itemPositionClassPrefix = $parentPositionClass ? $parentPositionClass . '-' : 'nav-';

        foreach ($children as $child) {
            $child->setLevel($childLevel);
            $child->setIsFirst($counter === 1);
            $child->setIsLast($counter === $childrenCount);
            $child->setPositionClass($itemPositionClassPrefix . $counter);

            $outermostClassCode = '';
            $outermostClass = $menuTree->getOutermostClass();

            if ($childLevel === 0 && $outermostClass) {
                $outermostClassCode = ' class="' . $outermostClass . '" ';
                $this->setCurrentClass($child, $outermostClass);
            }

            if ($this->shouldAddNewColumn($colBrakes, $counter)) {
                $html .= '</ul></li><li class="column"><ul>';
            }

            $html .= '<li ' . $this->_getRenderedMenuItemAttributes($child) . ' level='.$childLevel.'>';
			
				$html .= '<a href="' . $child->getUrl() . '" ' . $outermostClassCode . '>';
					if ( $child->getIsCategory() && ($catId = str_ireplace('category-node-', '', $child->getId())) ) {
						$category = $this->categoryFactory->create()->load($catId);
						if ($category->getData('icon')) {
							$html .= '<span class="menu-icon">'.$category->getData('icon').'</span>';
						}
					}
					$html .= '<span>'.$this->escapeHtml($child->getName()).'</span>';
				$html .= '</a>';
				
				$html .= $this->_addSubMenu(
					$child,
					$childLevel,
					$childrenWrapClass,
					$limit
				);
				
			$html .= '</li>';
            $counter++;
        }

        if (is_array($colBrakes) && !empty($colBrakes) && $limit) {
            $html = '<li class="column"><ul>' . $html . '</ul></li>';
        }

        return $html;
    }
	
    private function getChildLevel($parentLevel): int {
        return $parentLevel === null ? 0 : $parentLevel + 1;
    }
    private function removeChildrenWithoutActiveParent(Collection $children, int $childLevel): void {
        foreach ($children as $child) {
            if ($childLevel === 0 && $child->getData('is_parent_active') === false) {
                $children->delete($child);
            }
        }
    }
    private function setCurrentClass(Node $child, string $outermostClass): void {
        $currentClass = $child->getClass();
        if (empty($currentClass)) {
            $child->setClass($outermostClass);
        } else {
            $child->setClass($currentClass . ' ' . $outermostClass);
        }
    }
    private function shouldAddNewColumn(array $colBrakes, int $counter): bool {
        return count($colBrakes) && $colBrakes[$counter]['colbrake'];
    }
    // protected function _getRenderedMenuItemAttributes(Node $item) {
        // $html = '';
        // foreach ($this->_getMenuItemAttributes($item) as $attributeName => $attributeValue) {
            // $html .= ' ' . $attributeName . '="' . str_replace('"', '\"', $attributeValue) . '"';
        // }
        // return $html;
    // }
    // protected function _getMenuItemAttributes(Node $item) {
        // return ['class' => implode(' ', $this->_getMenuItemClasses($item))];
    // }
    // protected function _addSubMenu($child, $childLevel, $childrenWrapClass, $limit) {
        // $html = '';
        // if (!$child->hasChildren()) {
            // return $html;
        // }

        // $colStops = [];
        // if ($childLevel == 0 && $limit) {
            // $colStops = $this->_columnBrake($child->getChildren(), $limit);
        // }

        // $html .= '<ul class="level' . $childLevel . ' ' . $childrenWrapClass . '">';
        // $html .= $this->_getHtml($child, $childrenWrapClass, $limit, $colStops);
        // $html .= '</ul>';

        // return $html;
    // }
	
}