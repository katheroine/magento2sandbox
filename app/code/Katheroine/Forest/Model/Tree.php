<?php declare(strict_types=1);

namespace Katheroine\Forest\Model;

use Magento\Framework\Model\AbstractModel;

class Tree extends AbstractModel
{
    /**
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init('Katheroine\Forest\Model\ResourceModel\Tree');
    }
}
