<?php

declare(strict_types=1);

namespace Freento\CustomerLock\Controller\Adminhtml\Index;

use Magento\Backend\App\Action\Context;
use Magento\Customer\Controller\Adminhtml\Index\AbstractMassAction;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\State\InputMismatchException;
use Magento\Ui\Component\MassAction\Filter;
use Freento\CustomerLock\Helper\LockCustomer;

class MassLock extends AbstractMassAction implements HttpPostActionInterface
{
    /**
     * @var LockCustomer
     */
    private LockCustomer $lockCustomerHelper;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param LockCustomer $lockCustomer
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        LockCustomer $lockCustomer
    ) {
        parent::__construct($context, $filter, $collectionFactory);

        $this->lockCustomerHelper = $lockCustomer;
    }

    /**
     * Locks customers by id collection
     *
     * @param AbstractCollection $collection
     * @return Redirect
     * @throws InputException
     * @throws LocalizedException
     * @throws InputMismatchException
     */
    protected function massAction(AbstractCollection $collection): Redirect
    {
        if (!$this->_authorization->isAllowed('Freento_CustomerLock::action')) {
            $this->messageManager->addErrorMessage(__('Operation not permitted'));
            return $this->resultRedirectFactory->create()->setPath('customer/*/index');
        }

        foreach ($collection->getAllIds() as $id) {
            try {
                $this->lockCustomerHelper->lockCustomer($id);
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while editing the customer with id = %1.', $id)
                );
            }
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('customer/*/index');
        $this->messageManager->addSuccessMessage(__('Users were successfully locked'));
        return $resultRedirect;
    }
}
