<?php declare(strict_types=1);

namespace Katheroine\Forest\Controller\Trees;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Api\FilterFactory;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
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
     * @var SortOrderBuilder
     */
    private $sortOrderBuilder;

    /**
     * @var SearchCriteriaInterface
     */
    private $searchCriteria;

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
     * @param SortOrderBuilder $sortOrderBuilder
     * @param SearchCriteriaInterface $searchCriteria
     * @param TreeFactory $treeFactory
     * @param TreeRepository $treeRepository
     */
    public function __construct(
        Context $context,
        FilterFactory $filterFactory,
        FilterGroupBuilder $filterGroupBuilder,
        SortOrderBuilder $sortOrderBuilder,
        SearchCriteriaInterface $searchCriteria,
        TreeFactory $treeFactory,
        TreeRepository $treeRepository
    ) {
        $this->filterFactory = $filterFactory;
        $this->filterGroupBuilder = $filterGroupBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->searchCriteria = $searchCriteria;
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

        $invalidConditions = array_filter(
            $requestParams,
            [$this, 'searchConditionIsValid'],
            ARRAY_FILTER_USE_BOTH
        );

        return $invalidConditions;
    }

    /**
     * @param string $conditionValue
     * @param string $conditionFieldName
     * @return bool
     */
    private function searchConditionIsValid(
        string $conditionValue,
        string $conditionFieldName
    ): bool {
        return !in_array(
            $conditionFieldName,
            $this->treeFactory::TREE_FIELDS,
            \true
        );
    }

    private function renderInvalidConditions(): void
    {
        $invalidFields = $this->getInvalidFields();

        $message = $this->buildMessageFromInvalidFields($invalidFields);

        echo '<p>' . $message . '</p>';
    }

    /**
     * @return array
     */
    private function getInvalidFields(): array
    {
        return array_keys($this->invalidConditions);
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

    private function renderTreesList(): void
    {
        $trees = $this->loadTrees();

        echo $this->buildTreesTable($trees);
    }

    /**
     * @param array $trees
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
        $this->initSearchCriteria();

        return $this->treeRepository->getList($this->searchCriteria);
    }

    private function initSearchCriteria(): void
    {
        $this->fillFilterGroupBuilderWithFilters();
        $this->searchCriteria->setFilterGroups([
            $this->filterGroupBuilder->create()
        ]);

        $this->setupSortOrderBuilder();
        $this->searchCriteria->setSortOrders([
            $this->sortOrderBuilder->create()
        ]);
    }

    private function fillFilterGroupBuilderWithFilters(): void
    {
        $searchingConditions = $this->getTreeSearchingConditions();

        $filters = $this->buildFiltersFromConditions($searchingConditions);

        $this->filterGroupBuilder->setFilters($filters);
    }

    private function setupSortOrderBuilder(): void
    {
        $this->sortOrderBuilder
            ->setField('name')
            ->setAscendingDirection();
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
