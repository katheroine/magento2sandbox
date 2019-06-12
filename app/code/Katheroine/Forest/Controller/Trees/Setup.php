<?php declare(strict_types=1);

namespace Katheroine\Forest\Controller\Trees;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Katheroine\Forest\Model\TreeFactory;
use Katheroine\Forest\Model\ResourceModel\Tree as TreeResource;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

class Setup extends Action
{
    /**
     * @var TreeFactory
     */
    private $treeFactory;

    /**
     * @var TreeResource
     */
    private $treeResource;

    /**
     * @param Context $context
     * @param TreeFactory $treeFactory
     * @param TreeResource $treeResource
     */
    public function __construct(
        Context $context,
        TreeFactory $treeFactory,
        TreeResource $treeResource
    ) {
        $this->treeFactory = $treeFactory;
        $this->treeResource = $treeResource;

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface|void
     */
    public function execute()
    {
        $tree1 = $this->treeFactory->create();
        $tree1->setData('name', 'oak');
        $this->treeResource->save($tree1);
        $tree2 = $this->treeFactory->create();
        $tree2->setData('name', 'beech');
        $this->treeResource->save($tree2);
        $tree3 = $this->treeFactory->create();
        $tree3->setData('name', 'larch');
        $this->treeResource->save($tree3);

        echo 'Done.';
    }
}
