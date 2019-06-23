<?php declare(strict_types=1);

namespace Katheroine\Forest\Model;

use Magento\Framework\ObjectManagerInterface;

class TreeFactory
{
    /**
     * Fields of the tree entity
     */
    public const TREE_FIELDS = [
        'name',
        'type',
        'all-year',
        'description'
    ];

    /**
     * Types of the trees
     */
    public const TREE_TYPE_BROADLEAVED = 'broad-leaved';
    public const TREE_TYPE_CONIFEROUS = 'coniferous';

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
