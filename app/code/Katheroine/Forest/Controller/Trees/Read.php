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

        $invalidConditions = \array_filter(
            $requestParams,
            function (string $conditionValue, string $conditionFieldName): bool {
                return ($conditionFieldName !== 'id');
            },
            ARRAY_FILTER_USE_BOTH
        );

        $invalidFields = array_keys($invalidConditions);

        if (!empty($invalidFields)) {
            $pluralDetected = count($invalidFields) > 1;

            if ($pluralDetected) {
                $invalidFieldsListing = implode(', ', $invalidFields);
                $message = __('Trees entities have no fields ') . $invalidFieldsListing . '.';
            } else {
                $invalidField = $invalidFields[0];
                $message = __('Trees entities have no field ') . $invalidField . '.';
            }

            echo '<p>' . $message . '</p>';
        } else {
            if (!isset($requestParams['id'])) {
                echo '<p>' . __("There is no ID parametr given.") . '</p>';
            } else {
                $id = $requestParams['id'];

                try {
                    $tree = $this->treeRepository->getById((int)$id);

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
    }
}
