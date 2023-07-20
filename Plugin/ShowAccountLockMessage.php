<?php

declare(strict_types=1);

namespace Freento\CustomerLock\Plugin;

use Magento\Customer\Controller\Account\LoginPost;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\AuthenticationInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Customer\Model\Session;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Exception\NoSuchEntityException;

class ShowAccountLockMessage
{
    /**
     * @var CustomerRepositoryInterface
     */
    private CustomerRepositoryInterface $customerRepository;

    /**
     * @var AuthenticationInterface
     */
    private AuthenticationInterface $authentication;

    /**
     * @var MessageManagerInterface
     */
    private MessageManagerInterface $messageManager;

    /**
     * @var CustomerUrl
     */
    private CustomerUrl $customerUrl;

    /**
     * @var Session
     */
    private Session $session;

    /**
     * @var Validator
     */
    private Validator $formKeyValidator;

    /**
     * @var RedirectFactory
     */
    private RedirectFactory $resultRedirectFactory;

    /**
     * ShowAccountLockMessage constructor.
     * @param CustomerRepositoryInterface $customerRepository
     * @param AuthenticationInterface $authentication
     * @param MessageManagerInterface $messageManager
     * @param Session $customerSession
     * @param CustomerUrl $customerUrl
     * @param Validator $formKeyValidator
     * @param RedirectFactory $resultRedirectFactory
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        AuthenticationInterface $authentication,
        MessageManagerInterface $messageManager,
        Session $customerSession,
        CustomerUrl $customerUrl,
        Validator $formKeyValidator,
        RedirectFactory $resultRedirectFactory
    ) {
        $this->customerRepository = $customerRepository;
        $this->authentication = $authentication;
        $this->messageManager = $messageManager;
        $this->session = $customerSession;
        $this->customerUrl = $customerUrl;
        $this->formKeyValidator = $formKeyValidator;
        $this->resultRedirectFactory = $resultRedirectFactory;
    }

    /**
     * If the account is locked redirect to login page and show message
     *
     * @param LoginPost $subject
     * @param callable $proceed
     * @return Redirect
     * @throws LocalizedException
     */
    public function aroundExecute(LoginPost $subject, callable $proceed)
    {
        if ($this->session->isLoggedIn() || !$this->formKeyValidator->validate($subject->getRequest())) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*/*/');
            return $resultRedirect;
        }

        if ($subject->getRequest()->isPost()) {
            $login = $subject->getRequest()->getPost('login');
            if (!empty($login['username']) && !empty($login['password'])) {
                try {
                    $customer = $this->customerRepository->get($login['username']);
                } catch (NoSuchEntityException $e) {
                    return $proceed();
                }

                if ($this->authentication->isLocked($customer->getId())) {
                    $this->session->setUsername($login['username']);
                    $this->messageManager->addErrorMessage(__('Your account is restricted'));
                    $resultRedirect = $this->resultRedirectFactory->create();
                    $resultRedirect->setPath($this->customerUrl->getLoginUrl());

                    return $resultRedirect;
                }
            }
        }

        return $proceed();
    }
}
