<?php declare(strict_types=1);

namespace Katheroine\Forest\Model;

use Katheroine\Forest\Model\ResourceModel\Tree\CollectionFactory as TreeCollectionFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Katheroine\Forest\Model\ResourceModel\Tree\Collection;

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
     * @var TreeCollectionFactory
     */
    private $treeCollectionFactory;

    public function __construct(TreeCollectionFactory $treeCollectionFactory)
    {
        $this->treeCollectionFactory = $treeCollectionFactory;
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
}
