<?php declare(strict_types=1);

namespace Katheroine\Garden\Controller\Fruits;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Katheroine\Garden\Model\FruitFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

class Create extends Action
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
        $id = $this->getRequest()
            ->getParam('id');

        if (! is_null($id)) {
            echo __('ID cannot be chosen, it is generated automatically');

            return;
        }

        $fruit = $this->fruitFactory->create();

        $fields = $this->getRequest()->getParams();

        foreach ($fields as $fieldName => $fieldValue) {
            // There is no possibility to check if the entity has the given field.

            $fruit->setData($fieldName, $fieldValue);
        }

        try {
            $fruit->save();
        } catch (\Exception $exception) {
            echo __('Data saving couldn\'t be done.');
        }

        echo "<table><tr><td>id</td><td>name</td></tr>
                <tr><td>{$fruit->getData('id')}</td><td>{$fruit->getData('name')}</td></tr>
            </table>";
    }
}
