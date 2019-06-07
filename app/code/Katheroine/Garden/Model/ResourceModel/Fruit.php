<?php declare(strict_types=1);

namespace Katheroine\Garden\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Fruit extends AbstractDb
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(
            'katheroine_garden_fruits',
            'id'
        );
    }
}
