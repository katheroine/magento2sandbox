<?php declare(strict_types=1);

namespace Katheroine\Forest\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Tree extends AbstractDb
{
    protected function _construct(): void
    {
        $this->_init(
            'katheroine_forest_trees',
            'id'
        );
    }
}
