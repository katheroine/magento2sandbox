<?php declare(strict_types=1);

namespace Katheroine\Garden\Model\ResourceModel\Fruit;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(
            'Katheroine\Garden\Model\Fruit',
            'Katheroine\Garden\Model\ResourceModel\Fruit'
        );
    }
}
