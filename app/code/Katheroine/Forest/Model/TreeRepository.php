<?php declare(strict_types=1);

namespace Katheroine\Forest\Model;

use Katheroine\Forest\Model\ResourceModel\Tree\CollectionFactory as TreeCollectionFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Katheroine\Forest\Model\ResourceModel\Tree\Collection;

class TreeRepository
{
    /**
     * @var TreeFactory
     */
    private $treeFactory;

    /**
     * @var TreeCollectionFactory
     */
    private $treeCollectionFactory;

    public function __construct(
        TreeFactory $treeFactory,
        TreeCollectionFactory $treeCollectionFactory
    ) {
        $this->treeFactory = $treeFactory;
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
