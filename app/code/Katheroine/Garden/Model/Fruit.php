<?php declare(strict_types=1);

namespace Katheroine\Garden\Model;

use Magento\Framework\Model\AbstractModel;

class Fruit extends AbstractModel
{
    /**
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init('Katheroine\Garden\Model\ResourceModel\Fruit');
    }
}
