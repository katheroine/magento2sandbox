<?php declare(strict_types=1);

namespace Katheroine\Forest\Controller\Trees;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Katheroine\Forest\Model\TreeFactory;
use Katheroine\Forest\Model\TreeRepository;

class Index extends Action
{
    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var FilterGroup
     */
    private $filterGroup;

    /**
     * @var SearchCriteriaInterface
     */
    private $searchCriteria;

    /**
     * @var SortOrder
     */
    private $sortOrder;

    /**
     * @var TreeRepository
     */
    private $treeRepository;

    private $treeFactory;

    public function __construct(
        Context $context,
        Filter $filter,
        FilterGroup $filterGroup,
        SearchCriteriaInterface $searchCriteria,
        SortOrder $sortOrder,
        TreeRepository $treeRepository,
        TreeFactory $treeFactory
    ) {
        $this->filter = $filter;
        $this->filterGroup = $filterGroup;
        $this->searchCriteria = $searchCriteria;
        $this->sortOrder = $sortOrder;
        $this->treeRepository = $treeRepository;
        $this->treeFactory = $treeFactory;

        parent::__construct($context);
    }

    public function execute()
    {
        $this->filter->setField('name')
            ->setValue('larch')
            ->getConditionType('like');

        $this->filterGroup->setFilters([$this->filter]);

        $this->searchCriteria->setFilterGroups([$this->filterGroup]);

        $this->sortOrder
            ->setField('name')
            ->setDirection('ASC');

        $this->searchCriteria->setSortOrders([$this->sortOrder]);

        $this->searchCriteria->setCurrentPage(1);

        $trees = $this->treeRepository->getList($this->searchCriteria);

        echo '<table><tr><td>id</td><td>name</td></tr>';

        foreach($trees as $tree) {
            echo "<tr><td>{$tree->getData('id')}</td><td>{$tree->getData('name')}</td></tr>";
        }

        echo '</table>';
    }
}
