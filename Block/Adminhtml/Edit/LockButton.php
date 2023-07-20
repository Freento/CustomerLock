<?php

declare(strict_types=1);

namespace Freento\CustomerLock\Block\Adminhtml\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Customer\Block\Adminhtml\Edit\GenericButton;

class LockButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @var CustomerRegistry
     */
    private CustomerRegistry $customerRegistry;

    /**
     * @var AuthorizationInterface
     */
    private AuthorizationInterface $authorization;

    /**
     * Constructor
     *
     * @param Context $context
     * @param Registry $registry
     * @param CustomerRegistry $customerRegistry
     * @param AuthorizationInterface $authorization 
     */
    public function __construct(
        Context $context,
        Registry $registry,
        CustomerRegistry $customerRegistry,
        AuthorizationInterface $authorization
    ) {
        parent::__construct($context, $registry);
        $this->customerRegistry = $customerRegistry;
        $this->authorization = $authorization;
    }

    /**
     * Returns Unlock button data
     *
     * @return array
     * @throws NoSuchEntityException
     */
    public function getButtonData(): array
    {
        $customerId = $this->getCustomerId();
        $isAllowed = $this->authorization->isAllowed('Freento_CustomerLock::action');
        $data = [];
        if (!$customerId || !$isAllowed) {
            return $data;
        }
        $customer = $this->customerRegistry->retrieve($customerId);
        if (!$customer->isCustomerLocked()) {
            $data = [
                'label' => __('Lock'),
                'class' => 'lock lock-customer',
                'on_click' => sprintf("location.href = '%s';", $this->getLockUrl()),
                'sort_order' => 50,
            ];
        }

        return $data;
    }

    /**
     * Returns customer unlock action URL
     *
     * @return string
     */
    private function getLockUrl(): string
    {
        return $this->getUrl('customerlock/edit/lock', ['customer_id' => $this->getCustomerId()]);
    }
}
