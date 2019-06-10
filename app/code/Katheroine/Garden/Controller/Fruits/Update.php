<?php declare(strict_types=1);

namespace Katheroine\Garden\Controller\Fruits;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Katheroine\Garden\Model\FruitFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

class Update extends Action
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

        if (is_null($id)) {
            echo __('Please, give the ID of the chosen fruit');

            return;
        }

        $fruit = $this->fruitFactory->create()
            ->load($id);

        if (empty($fruit->getId())) {
            echo __("There's no fruit with ID {$id}");

            return;
        }

        $fields = $this->getRequest()->getParams();
        unset($fields['id']);

        foreach ($fields as $fieldName => $fieldValue) {
            if (! $fruit->hasData($fieldName)) {
                echo __("Fruit whit ID {$id} has no field {$fieldName}.");

                return;
            }

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
