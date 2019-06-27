<?php declare(strict_types=1);

namespace Katheroine\Forest\Model;

use Katheroine\Forest\Model\ResourceModel\Tree\CollectionFactory as TreeCollectionFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Katheroine\Forest\Model\ResourceModel\Tree\Collection;
use Katheroine\Forest\Model\ResourceModel\Tree as TreeResource;

class TreeRepository
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
     * @var TreeFactory
     */
    private $treeFactory;

    /**
     * @var TreeCollectionFactory
     */
    private $treeCollectionFactory;

    /**
     * @var TreeResource
     */
    private $treeResource;

    /**
     * TreeRepository constructor.
     * @param TreeFactory $treeFactory
     * @param TreeCollectionFactory $treeCollectionFactory
     * @param TreeResource $treeResource
     */
    public function __construct(
        TreeFactory $treeFactory,
        TreeCollectionFactory $treeCollectionFactory,
        TreeResource $treeResource
    ) {
        $this->treeFactory = $treeFactory;
        $this->treeCollectionFactory = $treeCollectionFactory;
        $this->treeResource = $treeResource;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return Collection
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->treeCollectionFactory->create();

        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            $filters = $filterGroup->getFilters();
            foreach ($filters as $filter) {
                $collection->addFieldToFilter($filter->getField(), $filter->getValue());
            }
        }

        return $collection;
    }

    /**
     * @param int $id
     * @return Tree
     */
    public function getById(int $id)
    {
        $tree = $this->treeFactory->create();

        $this->treeResource->load(
            $tree,
            $id
        );

        return $tree;
    }
}
