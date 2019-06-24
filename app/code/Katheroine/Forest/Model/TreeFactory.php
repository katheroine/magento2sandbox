<?php declare(strict_types=1);

namespace Katheroine\Forest\Model;

use Magento\Framework\ObjectManagerInterface;

class TreeFactory
{
    /**
     * Object Manager instance
     *
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Type of the instance to create
     *
     * @var string
     */
    protected $instanceName;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param string $instanceName
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        $instanceName = Tree::class
    ) {
        $this->objectManager = $objectManager;
        $this->instanceName = $instanceName;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param array $data
     * @return Tree
     */
    public function create(array $data = []): Tree
    {
        return $this->objectManager->create($this->instanceName, $data);
    }
}
