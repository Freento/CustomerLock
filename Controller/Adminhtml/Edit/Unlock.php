<?php

declare(strict_types=1);

namespace Freento\CustomerLock\Controller\Adminhtml\Edit;

use Magento\Customer\Model\AuthenticationInterface;
use Magento\Backend\App\Action;

class Unlock extends \Magento\Customer\Controller\Adminhtml\Locks\Unlock
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Freento_CustomerLock::action';
}
