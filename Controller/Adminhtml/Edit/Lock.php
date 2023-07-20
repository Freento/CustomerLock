<?php

declare(strict_types=1);

namespace Freento\CustomerLock\Controller\Adminhtml\Edit;

use Freento\CustomerLock\Helper\LockCustomer;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;

class Lock extends Action implements HttpGetActionInterface
{
    public const ADMIN_RESOURCE = 'Freento_CustomerLock::action';

    /**
     * @var LockCustomer
     */
    private LockCustomer $lockCustomerHelper;

    /**
     * Lock constructor.
     *
     * @param Context $context
     * @param LockCustomer $lockCustomer
     */
    public function __construct(
        Context $context,
        LockCustomer $lockCustomer
    ) {
        parent::__construct($context);

        $this->lockCustomerHelper = $lockCustomer;
    }

    /**
     * Locks customer by id
     *
     * @return Redirect
     */
    public function execute(): Redirect
    {
        $customerId = $this->getRequest()->getParam('customer_id');
        try {
            if ($customerId) {
                $this->lockCustomerHelper->lockCustomer($customerId);
                $this->getMessageManager()->addSuccessMessage(__('Customer has been locked successfully.'));
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e);
        }

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath(
            'customer/index/edit',
            ['id' => $customerId]
        );
    }
}
