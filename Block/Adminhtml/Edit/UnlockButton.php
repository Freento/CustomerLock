<?php

declare(strict_types=1);

namespace Freento\CustomerLock\Block\Adminhtml\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Registry;
use Magento\Customer\Model\CustomerRegistry;

class UnlockButton extends \Magento\Customer\Block\Adminhtml\Edit\UnlockButton
{
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
        parent::__construct($context, $registry, $customerRegistry);
        $this->authorization = $authorization;
    }

    /**
     * @inheritDoc
     */
    public function getButtonData()
    {
        return $this->authorization->isAllowed('Freento_CustomerLock::action')
            ? parent::getButtonData()
            : [];
    }

    /**
     * Returns customer unlock action URL
     *
     * @return string
     */
    protected function getUnlockUrl(): string
    {
        return $this->getUrl('customerlock/edit/unlock', ['customer_id' => $this->getCustomerId()]);
    }
}
