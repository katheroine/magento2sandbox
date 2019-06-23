<?php declare(strict_types=1);

namespace Katheroine\Forest\Model\ResourceModel\Tree;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(
            'Katheroine\Forest\Model\Tree',
            'Katheroine\Forest\Model\ResourceModel\Tree'
        );
    }
}
