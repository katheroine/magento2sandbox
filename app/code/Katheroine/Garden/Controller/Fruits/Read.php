<?php declare(strict_types=1);

namespace Katheroine\Garden\Controller\Fruits;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Katheroine\Garden\Model\FruitFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

class Read extends Action
{
    /**
     * @var FruitFactory
     */
    private $fruitFactory;

    public function __construct(
        Context $context,
        FruitFactory $fruitFactory
    ) {
        $this->fruitFactory = $fruitFactory;

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface|void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');

        if (is_null($id)) {
            echo __('Please, give the ID of the chosen fruit');

            return;
        }

        // It's naive solution.
        // There should not be loaded more records than defined by the search criteria
        // (in this case: ID).
        $fruits = $this->fruitFactory->create()
            ->getCollection()
            ->getItemsByColumnValue('id', $id);

        if (empty($fruits)) {
            echo __("There's no fruit with ID {$id}");

            return;
        }

        $fruit = $fruits[0];

        echo "<table><tr><td>id</td><td>name</td></tr>
                <tr><td>{$fruit->getData('id')}</td><td>{$fruit->getData('name')}</td></tr>
            </table>";
    }
}
