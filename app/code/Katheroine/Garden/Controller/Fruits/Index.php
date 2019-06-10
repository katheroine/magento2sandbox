<?php declare(strict_types=1);

namespace Katheroine\Garden\Controller\Fruits;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Katheroine\Garden\Model\FruitFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

class Index extends Action
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
     * @throws \Exception
     */
    public function execute()
    {
        $fruit = $this->fruitFactory->create();
        $fruits = $fruit->getCollection();

        echo '<table><tr><td>id</td><td>name</td></tr>';

        foreach($fruits as $fruit) {
            echo "<tr><td>{$fruit->getData('id')}</td><td>{$fruit->getData('name')}</td></tr>";
        }

        echo '</table>';
    }
}
