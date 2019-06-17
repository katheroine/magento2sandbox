<?php declare(strict_types=1);

namespace Katheroine\Forest\Controller\Trees;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Api\FilterFactory;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Katheroine\Forest\Model\TreeFactory;
use Katheroine\Forest\Model\TreeRepository;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Katheroine\Forest\Model\Tree;

class Index extends Action
{
    /**
     * @var FilterFactory
     */
    private $filterFactory;

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
     * @var TreeFactory
     */
    private $treeFactory;

    /**
     * @var TreeRepository
     */
    private $treeRepository;


    /**
     * @param Context $context
     * @param FilterFactory $filterFactory
     * @param FilterGroup $filterGroup
     * @param SearchCriteriaInterface $searchCriteria
     * @param SortOrder $sortOrder
     * @param TreeFactory $treeFactory
     * @param TreeRepository $treeRepository
     */
    public function __construct(
        Context $context,
        FilterFactory $filterFactory,
        FilterGroup $filterGroup,
        SearchCriteriaInterface $searchCriteria,
        SortOrder $sortOrder,
        TreeFactory $treeFactory,
        TreeRepository $treeRepository
    ) {
        $this->filterFactory = $filterFactory;
        $this->filterGroup = $filterGroup;
        $this->searchCriteria = $searchCriteria;
        $this->sortOrder = $sortOrder;
        $this->treeFactory = $treeFactory;
        $this->treeRepository = $treeRepository;

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface|void
     */
    public function execute()
    {
        $invalidConditions = $this->getInvalidConditions();

        if (!empty($invalidConditions)) {
            $this->renderInvalidConditions($invalidConditions);

            return;
        }

        $this->initSearchCriteria();
        $trees = $this->treeRepository->getList($this->searchCriteria);

        $this->renderTreesList($trees);
    }

    private function initSearchCriteria(): void
    {
        $this->initFilterGroup();
        $this->searchCriteria->setFilterGroups([
            $this->filterGroup
        ]);

        $this->initSortOrder();
        $this->searchCriteria->setSortOrders([$this->sortOrder]);

        $this->searchCriteria->setCurrentPage(1);
    }

    private function initFilterGroup(): void
    {
        $searchingConditions = $this->getTreeSearchingConditions();

        $filters = $this->buildFiltersFromConditions($searchingConditions);

        $this->filterGroup->setFilters($filters);
    }

    private function initSortOrder(): void
    {
        $this->sortOrder
            ->setField('name')
            ->setDirection('ASC');
    }

    /**
     * @return array
     */
    private function getInvalidConditions(): array
    {
        $requestParams = $this->getRequest()
            ->getParams();

        $invalidConditions = array_filter($requestParams, function($paramValue, $paramKey) {
            return !in_array($paramKey, $this->treeFactory::TREE_FIELDS, \true);
        }, ARRAY_FILTER_USE_BOTH);

        return $invalidConditions;
    }

    /**
     * @return array
     */
    private function getTreeSearchingConditions(): array
    {
        $requestParams = $this->getRequest()
            ->getParams();

        $treeConditions = array_filter($requestParams, function($paramValue, $paramKey) {
            return in_array($paramKey, $this->treeFactory::TREE_FIELDS, \true);
        }, ARRAY_FILTER_USE_BOTH);

        return $treeConditions;
    }

    /**
     * @param $fields
     * @return array|void
     */
    public function buildFiltersFromConditions($fields)
    {
        $filters = [];

        foreach ($fields as $fieldName => $fieldValue) {
            if (! in_array($fieldName, $this->treeFactory::TREE_FIELDS, \true)) {
                echo __("Trees entities have no fields {$fieldName}.");

                return;
            }

            $filter = $this->filterFactory->create();

            $filter->setField($fieldName)
                ->setValue($fieldValue)
                ->getConditionType('=');

            $filters[] = $filter;
        }

        return $filters;
    }

    /**
     * @param array $invalidConditions
     */
    private function renderInvalidConditions($invalidConditions): void
    {
        $invalidFields = array_keys($invalidConditions);

        $pluralDetected = count($invalidFields) > 1;

        if ($pluralDetected) {
            $invalidFieldsListing = implode(', ', $invalidFields);
            $message = __('Trees entities have no fields ') . $invalidFieldsListing .'.';
        } else {
            $invalidField = $invalidFields[0];
            $message = __('Trees entities have no field ') . $invalidField .'.';
        }

        echo '<p>' . $message . '</p>';
    }

    /**
     * @param Tree[] $trees
     */
    private function renderTreesList($trees): void
    {
        echo '<table><tr><td>id</td><td>name</td></tr>';

        foreach($trees as $tree) {
            echo "<tr><td>{$tree->getData('id')}</td><td>{$tree->getData('name')}</td></tr>";
        }

        echo '</table>';
    }
}
