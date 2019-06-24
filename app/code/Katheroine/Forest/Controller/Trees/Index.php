<?php declare(strict_types=1);

namespace Katheroine\Forest\Controller\Trees;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Katheroine\Forest\Model\TreeRepository;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Katheroine\Forest\Model\ResourceModel\Tree\Collection as TreeCollection;
use Magento\Framework\Api\Filter;

class Index extends Action
{
    /**
     * @var $filterBuilder
     */
    private $filterBuilder;

    /**
     * @var FilterGroupBuilder
     */
    private $filterGroupBuilder;

    /**
     * @var SortOrderBuilder
     */
    private $sortOrderBuilder;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

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
     * @param FilterBuilder $filterBuilder
     * @param FilterGroupBuilder $filterGroupBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param TreeRepository $treeRepository
     */
    public function __construct(
        Context $context,
        FilterBuilder $filterBuilder,
        FilterGroupBuilder $filterGroupBuilder,
        SortOrderBuilder $sortOrderBuilder,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        TreeRepository $treeRepository
    ) {
        $this->filterBuilder = $filterBuilder;
        $this->filterGroupBuilder = $filterGroupBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
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

    /**
     * @return void
     */
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

        $invalidConditions = \array_filter(
            $requestParams,
            [$this, 'isSearchConditionInvalid'],
            ARRAY_FILTER_USE_BOTH
        );

        return $invalidConditions;
    }

    /**
     * @return void
     */
    private function renderInvalidConditions(): void
    {
        $invalidFields = array_keys($this->invalidConditions);

        $message = $this->buildMessageFromInvalidFields($invalidFields);

        echo '<p>' . $message . '</p>';
    }

    /**
     * @param array $invalidFields
     * @return string
     */
    private function buildMessageFromInvalidFields(array $invalidFields): string
    {
        $pluralDetected = count($invalidFields) > 1;

        if ($pluralDetected) {
            $message = $this->buildPluralFormMessageFromInvalidFields($invalidFields);
        } else {
            $message = $this->buildSingularFormMessageFromInvalidFields($invalidFields);
        }

        return $message;
    }

    /**
     * @param array $invalidFields
     * @return string
     */
    private function buildPluralFormMessageFromInvalidFields(array $invalidFields): string
    {
        $invalidFieldsListing = implode(', ', $invalidFields);
        $message = __('Trees entities have no fields ') . $invalidFieldsListing .'.';

        return $message;
    }

    /**
     * @param array $invalidFields
     * @return string
     */
    private function buildSingularFormMessageFromInvalidFields(array $invalidFields): string
    {
        $invalidField = $invalidFields[0];
        $message = __('Trees entities have no field ') . $invalidField .'.';

        return $message;
    }

    /**
     * @return void
     */
    private function renderTreesList(): void
    {
        $trees = $this->loadTrees();

        echo $this->buildTreesTable($trees);
    }

    /**
     * @param TreeCollection $trees
     * @return string
     */
    private function buildTreesTable(TreeCollection $trees): string
    {
        $header = '<tr><td>id</td><td>name</td></tr>';

        $rows = '';

        foreach ($trees as $tree) {
            $rows .= "<tr><td>{$tree->getData('id')}</td><td>{$tree->getData('name')}</td></tr>";
        }

        $table = '<table>' . $header . $rows . '</table>';

        return $table;
    }

    /**
     * @return TreeCollection
     */
    private function loadTrees(): TreeCollection
    {
        $this->setupSearchCriteriaBuilder();

        return $this->treeRepository->getList(
            $this->searchCriteriaBuilder->create()
        );
    }

    /**
     * @return void
     */
    private function setupSearchCriteriaBuilder(): void
    {
        $this->setFilterGroupWithinSearchCriteriaBuilder();
        $this->setSortOrderWithinSearchCriteriaBuilder();
    }

    /**
     * @return void
     */
    private function setFilterGroupWithinSearchCriteriaBuilder(): void
    {
        $this->setFiltersWithinFilterGroupBuilder();
        $this->searchCriteriaBuilder->setFilterGroups([
            $this->filterGroupBuilder->create()
        ]);
    }

    /**
     * @return void
     */
    private function setFiltersWithinFilterGroupBuilder(): void
    {
        $searchingConditions = $this->getTreeSearchingConditions();

        $filters = $this->buildFiltersFromConditions($searchingConditions);

        $this->filterGroupBuilder->setFilters($filters);
    }

    /**
     * @return array
     */
    private function getTreeSearchingConditions(): array
    {
        $requestParams = $this->getRequest()
            ->getParams();

        $treeConditions = \array_filter(
            $requestParams,
            [$this, 'isSearchConditionValid'],
            ARRAY_FILTER_USE_BOTH);

        return $treeConditions;
    }

    /**
     * @param array $conditions
     * @return array
     */
    private function buildFiltersFromConditions(array $conditions): array
    {
        $filters = [];

        foreach ($conditions as $conditionFieldName => $conditionValue) {
            $filters[] = $this->buildFilterFromConditionFieldNameAndValue(
                $conditionFieldName,
                $conditionValue
            );
        }

        return $filters;
    }

    /**
     * @param string $conditionFieldName
     * @param string $conditionValue
     * @return Filter
     */
    private function buildFilterFromConditionFieldNameAndValue(
        string $conditionFieldName,
        string $conditionValue
    ): Filter {
        return $this->filterBuilder
            ->setField($conditionFieldName)
            ->setValue($conditionValue)
            ->setConditionType('=')
            ->create();
    }

    /**
     * @return void
     */
    private function setSortOrderWithinSearchCriteriaBuilder(): void
    {
        $this->setupSortOrderBuilder();
        $this->searchCriteriaBuilder->setSortOrders([
            $this->sortOrderBuilder->create()
        ]);
    }

    /**
     * @return void
     */
    private function setupSortOrderBuilder(): void
    {
        $this->sortOrderBuilder
            ->setField('name')
            ->setAscendingDirection();
    }

    /**
     * @param string $conditionValue
     * @param string $conditionFieldName
     * @return bool
     */
    private function isSearchConditionValid(
        string $conditionValue,
        string $conditionFieldName
    ): bool {
        return \in_array(
            $conditionFieldName,
            $this->treeRepository::TREE_FIELDS,
            \true
        );
    }

    /**
     * @param string $conditionValue
     * @param string $conditionFieldName
     * @return bool
     */
    private function isSearchConditionInvalid(
        string $conditionValue,
        string $conditionFieldName
    ): bool {
        return !\in_array(
            $conditionFieldName,
            $this->treeRepository::TREE_FIELDS,
            \true
        );
    }
}
