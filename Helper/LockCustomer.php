<?php

declare(strict_types=1);

namespace Freento\CustomerLock\Helper;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Intl\DateTimeFactory;
use Magento\Framework\Stdlib\DateTime;
use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Magento\Customer\Model\CustomerAuthUpdate;
use Magento\Customer\Model\CustomerRegistry;

class LockCustomer
{
    /**
     * @var CustomerRepository
     */
    private CustomerRepository $customerRepository;

    /**
     * @var DateTimeFactory
     */
    private DateTimeFactory $dateTimeFactory;

    /**
     * @var CustomerRegistry
     */
    private CustomerRegistry $customerRegistry;

    /**
     * @var CustomerAuthUpdate
     */
    private CustomerAuthUpdate $customerAuthUpdate;

    /**
     * @param CustomerRepository $customerRepository
     * @param DateTimeFactory $dateTimeFactory
     * @param CustomerRegistry $customerRegistry
     * @param CustomerAuthUpdate $customerAuthUpdate
     */
    public function __construct(
        CustomerRepository $customerRepository,
        DateTimeFactory $dateTimeFactory,
        CustomerRegistry $customerRegistry,
        CustomerAuthUpdate $customerAuthUpdate
    ) {
        $this->customerRepository = $customerRepository;
        $this->dateTimeFactory = $dateTimeFactory;
        $this->customerRegistry = $customerRegistry;
        $this->customerAuthUpdate = $customerAuthUpdate;
    }

    /**
     * Unlocks customer by id
     *
     * @param string $id
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function unlockCustomer(string $id): void
    {
        $customerSecure = $this->customerRegistry->retrieveSecureData($id);
        $customerSecure->setFailuresNum(0)
            ->setFirstFailure(null)
            ->setLockExpires(null);
        $this->customerAuthUpdate->saveAuth($id);
    }

    /**
     * Locks customer by id
     *
     * @param string $id
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function lockCustomer(string $id): void
    {
        $customerSecure = $this->customerRegistry->retrieveSecureData($id);

        $date = $this->dateTimeFactory->create();
        $customerSecure->setFailuresNum(10)
            ->setFirstFailure($date->modify('-5 minutes')->format(DateTime::DATETIME_PHP_FORMAT))
            ->setLockExpires($date->add(new \DateInterval('P10Y'))->format(DateTime::DATETIME_PHP_FORMAT));
        $this->customerAuthUpdate->saveAuth($id);
    }
}
