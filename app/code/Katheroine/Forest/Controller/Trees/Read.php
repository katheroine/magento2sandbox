<?php declare(strict_types=1);

namespace Katheroine\Forest\Controller\Trees;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Katheroine\Forest\Model\TreeRepository;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

class Read extends Action
{
    /**
     * @var TreeRepository
     */
    private $treeRepository;

    /**
     * @param Context $context
     * @param TreeRepository $treeRepository
     */
    public function __construct(
        Context $context,
        TreeRepository $treeRepository
    ) {
        $this->treeRepository = $treeRepository;

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface|void
     */
    public function execute()
    {
        $requestParams = $this->getRequest()
            ->getParams();

        $invalidConditions = $this->extractInvalidConditionsFromRequestParams($requestParams);

        if (!empty($invalidConditions)) {
            $this->renderInvalidConditions($invalidConditions);
        } elseif (!isset($requestParams['id'])) {
            $this->renderNoIdParameterMessage();
        } else {
            $id = (int) $requestParams['id'];
            $this->renderTreeOfGivenId($id);
        }
    }

    /**
     * @param array $requestParams
     * @return string[]
     */
    private function extractInvalidConditionsFromRequestParams(array $requestParams): array
    {
        return \array_filter(
            $requestParams,
            [$this, 'isSearchConditionInvalid'],
            ARRAY_FILTER_USE_BOTH
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
        return ($conditionFieldName !== 'id');
    }

    /**
     * @param array $invalidConditions
     * @return void
     */
    private function renderInvalidConditions(array $invalidConditions): void
    {
        $invalidFields = array_keys($invalidConditions);

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
    private function renderNoIdParameterMessage(): void
    {
        echo '<p>' . __("There is no ID parametr given.") . '</p>';
    }

    /**
     * @param int $id
     * @return void
     */
    private function renderTreeOfGivenId(int $id): void
    {
        try {
            $tree = $this->treeRepository->getById($id);

            echo "<h2>Tree ID: {$id}</h2>
            <table>
                <tr><td>id: </td><td>{$tree->getId()}</td></tr>
                <tr><td>name: </td><td>{$tree->getData('name')}</td></tr>
                <tr><td>type: </td><td>{$tree->getData('type')}</td></tr>
                <tr><td>all-year: </td><td>{$tree->getData('all-year')}</td></tr>
                <tr><td>description: </td><td>{$tree->getData('description')}</td></tr>
            </table>";
        } catch (NoSuchEntityException $exception) {
            echo '<p>' . __('No such entity with ID ') . $id . '</p>';
        }
    }
}
