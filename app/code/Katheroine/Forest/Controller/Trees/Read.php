<?php declare(strict_types=1);

namespace Katheroine\Forest\Controller\Trees;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Katheroine\Forest\Model\TreeRepository;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Katheroine\Forest\Model\Tree;
use Magento\Framework\Phrase;

class Read extends Action
{
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
    private $redundantConditions;

    /**
     * @var bool
     */
    private $idIsPassed;

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
        $this->validateConditions();

        if (!$this->conditionsAreValid) {
            $this->renderInvalidConditions();

            return;
        }

        $this->renderTree();
    }

    /**
     * @return void
     */
    private function validateConditions(): void
    {
        $this->redundantConditions = $this->getRedundantConditions();
        $idParam = $this->getRequest()
            ->getParam('id');
        $this->idIsPassed = ($idParam !== \null);

        $this->conditionsAreValid = empty($this->redundantConditions)
            && $this->idIsPassed;
    }

    /**
     * @return string[]
     */
    private function getRedundantConditions(): array
    {
        $requestParams = $this->getRequest()
            ->getParams();

        $redundantConditions = \array_filter(
            $requestParams,
            [$this, 'isSearchConditionInvalid'],
            ARRAY_FILTER_USE_BOTH
        );

        return $redundantConditions;
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
     * @return void
     */
    private function renderInvalidConditions(): void
    {
        if (! empty($this->redundantConditions)) {
            $message = $this->buildMessageForRedundantConditions($this->redundantConditions);
        } elseif (! $this->idIsPassed) {
            $message = $this->buildMessageForNoIdParameter();
        }

        echo '<p>' . $message . '</p>';
    }

    /**
     * @param array $redundantConditions
     * @return string
     */
    private function buildMessageForRedundantConditions(array $redundantConditions): string
    {
        $redundantFields = \array_keys($redundantConditions);

        $message = $this->buildMessageFromRedundantFields($redundantFields);

        return $message;
    }

    /**
     * @param array $redundantFields
     * @return string
     */
    private function buildMessageFromRedundantFields(array $redundantFields): string
    {
        $pluralDetected = \count($redundantFields) > 1;

        if ($pluralDetected) {
            $message = $this->buildPluralFormMessageFromRedundantFields($redundantFields);
        } else {
            $message = $this->buildSingularFormMessageFromRedundantFields($redundantFields);
        }

        return $message;
    }

    /**
     * @param array $redundantFields
     * @return string
     */
    private function buildPluralFormMessageFromRedundantFields(array $redundantFields): string
    {
        $invalidFieldsListing = implode(', ', $redundantFields);
        $message = __('Trees entities have no fields ') . $invalidFieldsListing .'.';

        return $message;
    }

    /**
     * @param array $redundantFields
     * @return string
     */
    private function buildSingularFormMessageFromRedundantFields(array $redundantFields): string
    {
        $redundantField = $redundantFields[0];
        $message = __('Trees entities have no field ') . $redundantField . '.';

        return $message;
    }

    /**
     * @return Phrase
     */
    private function buildMessageForNoIdParameter(): Phrase
    {
        return __('There is no ID parametr given.');
    }

    /**
     * @return void
     */
    private function renderTree(): void
    {
        $id = (int) $this->getRequest()
            ->getParam('id');
        $tree = $this->treeRepository->getById($id);

        if ($tree->isEmpty()) {
            echo '<p>' . __('No such entity with ID ') . $id . '</p>';
        } else {
            echo "<h2>Tree ID: {$id}</h2>" . $this->buildTreeTable($tree);
        }
    }

    /**
     * @param Tree $tree
     * @return string
     */
    private function buildTreeTable(Tree $tree): string
    {
        $table = "<table>
                <tr><td>name: </td><td>{$tree->getData('name')}</td></tr>
                <tr><td>type: </td><td>{$tree->getData('type')}</td></tr>
                <tr><td>all-year: </td><td>{$tree->getData('all-year')}</td></tr>
                <tr><td>description: </td><td>{$tree->getData('description')}</td></tr>
            </table>";

        return $table;
    }
}
