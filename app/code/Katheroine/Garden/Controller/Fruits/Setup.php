<?php declare(strict_types=1);

namespace Katheroine\Garden\Controller\Fruits;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Katheroine\Garden\Model\FruitFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

class Setup extends Action
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
        $fruit1 = $this->fruitFactory->create();
        $fruit1->setData('name', 'orange');
        $fruit1->save();
        $fruit2 = $this->fruitFactory->create();
        $fruit2->setData('name', 'apple');
        $fruit2->save();
        $fruit3 = $this->fruitFactory->create();
        $fruit3->setData('name', 'banana');
        $fruit3->save();

        echo 'Done.';
    }
}
