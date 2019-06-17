<?php declare(strict_types=1);

namespace Katheroine\Forest\Controller\Trees;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Katheroine\Forest\Model\TreeFactory;
use Katheroine\Forest\Model\ResourceModel\Tree as TreeResource;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\AlreadyExistsException;

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
     * @throws AlreadyExistsException
     */
    public function execute()
    {
        $this->createTree('oak', $this->treeFactory::TREE_TYPE_BROADLEAVED, false);
        $this->createTree('beech', $this->treeFactory::TREE_TYPE_BROADLEAVED, false);
        $this->createTree('larch', $this->treeFactory::TREE_TYPE_CONIFEROUS, true);

        echo 'Done.';
    }

    /**
     * @param string $name
     * @param string $type
     * @param bool $allYear
     * @throws AlreadyExistsException
     */
    private function createTree(
        string $name,
        string $type,
        bool $allYear
    ) {
        $tree = $this->treeFactory->create();
        $tree->setData('name', $name);
        $tree->setData('type', $type);
        $tree->setData('all-year', $allYear);
        $this->treeResource->save($tree);
    }
}
