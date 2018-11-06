<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Controller\Adminhtml\Replicate\Orders;

use Praxigento\Odoo\Config as Cfg;

class Report
    extends \Praxigento\Core\App\Action\Back\Base
{

    public function __construct(
        \Magento\Backend\App\Action\Context $context
    ) {
        $aclResource = Cfg::MODULE . '::' . Cfg::ACL_REPLICATE;
        $activeMenu = Cfg::MODULE . '::' . Cfg::MENU_REPLICATE_ORDERS;
        $breadcrumbLabel = 'Replicate Sale Orders';
        $breadcrumbTitle = 'Replicate Sale Orders';
        $pageTitle = 'Replicate Sale Orders';
        parent::__construct(
            $context,
            $aclResource,
            $activeMenu,
            $breadcrumbLabel,
            $breadcrumbTitle,
            $pageTitle
        );
    }
}