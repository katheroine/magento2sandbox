<?php declare(strict_types=1);

namespace Katheroine\Forest\Controller\Trees;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Api\FilterFactory;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Katheroine\Forest\Model\TreeFactory;
use Katheroine\Forest\Model\TreeRepository;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Katheroine\Forest\Model\ResourceModel\Tree\Collection as TreeCollection;

class Index extends Action
{
    /**
     * @var FilterFactory
     */
    private $filterFactory;

    /**
     * @var FilterGroupBuilder
     */
    private $filterGroupBuilder;

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
     * @var null|bool
     */
    private $conditionsAreValid;

    /**
     * @var null|array
     */
    private $invalidConditions;


    /**
     * @param Context $context
     * @param FilterFactory $filterFactory
     * @param FilterGroupBuilder $filterGroupBuilder
     * @param SearchCriteriaInterface $searchCriteria
     * @param SortOrder $sortOrder
     * @param TreeFactory $treeFactory
     * @param TreeRepository $treeRepository
     */
    public function __construct(
        Context $context,
        FilterFactory $filterFactory,
        FilterGroupBuilder $filterGroupBuilder,
        SearchCriteriaInterface $searchCriteria,
        SortOrder $sortOrder,
        TreeFactory $treeFactory,
        TreeRepository $treeRepository
    ) {
        $this->filterFactory = $filterFactory;
        $this->filterGroupBuilder = $filterGroupBuilder;
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
        $this->validateConditions();

        if (!$this->conditionsAreValid) {
            $this->renderInvalidConditions();

            return;
        }

        $this->renderTreesList();
    }

    private function validateConditions(): void
    {
        $this->invalidConditions = $this->getInvalidConditions();

        $this->conditionsAreValid = empty($this->invalidConditions);
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

    private function renderInvalidConditions(): void
    {
        $invalidFields = array_keys($this->invalidConditions);

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

    private function renderTreesList(): void
    {
        $trees = $this->loadTrees();

        echo '<table><tr><td>id</td><td>name</td></tr>';

        foreach($trees as $tree) {
            echo "<tr><td>{$tree->getData('id')}</td><td>{$tree->getData('name')}</td></tr>";
        }

        echo '</table>';
    }

    /**
     * @return TreeCollection
     */
    private function loadTrees(): TreeCollection
    {
        $this->initSearchCriteria();

        return $this->treeRepository->getList($this->searchCriteria);
    }

    private function initSearchCriteria(): void
    {
        $this->fillFilterGroupBuilderWithFilters();
        $this->searchCriteria->setFilterGroups([
            $this->filterGroupBuilder->create()
        ]);

        $this->initSortOrder();
        $this->searchCriteria->setSortOrders([$this->sortOrder]);

        $this->searchCriteria->setCurrentPage(1);
    }

    private function fillFilterGroupBuilderWithFilters(): void
    {
        $searchingConditions = $this->getTreeSearchingConditions();

        $filters = $this->buildFiltersFromConditions($searchingConditions);

        $this->filterGroupBuilder->setFilters($filters);
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
     * @return array
     */
    private function buildFiltersFromConditions(array $fields): array
    {
        $filters = [];

        foreach ($fields as $fieldName => $fieldValue) {
            $filter = $this->filterFactory->create();

            $filter->setField($fieldName)
                ->setValue($fieldValue)
                ->setConditionType('=');

            $filters[] = $filter;
        }

        return $filters;
    }
}
