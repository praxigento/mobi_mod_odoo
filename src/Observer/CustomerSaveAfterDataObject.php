<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Select all orders for newly signed customers and push it to Odoo.
 */
class CustomerSaveAfterDataObject implements ObserverInterface
{
    /** @var  \Praxigento\Odoo\Service\IReplicate */
    protected $callReplicate;
    /** @var \Magento\Sales\Api\OrderRepositoryInterface */
    protected $repoOrder;
    /** @var \Magento\Framework\ObjectManagerInterface */
    protected $manObj;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Magento\Sales\Api\OrderRepositoryInterface $repoOrder,
        \Praxigento\Odoo\Service\IReplicate $callReplicate
    ) {
        $this->manObj = $manObj;
        $this->repoOrder = $repoOrder;
        $this->callReplicate = $callReplicate;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Customer\Model\Data\Customer $beforeSave */
        $beforeSave = $observer->getData('orig_customer_data_object');
        /** @var \Magento\Customer\Model\Data\Customer $afterSave */
        $afterSave = $observer->getData('customer_data_object');
        $idBefore = $beforeSave->getId();
        $idAfter = $afterSave->getId();
        if ($idBefore != $idAfter) {
            /* this is newly saved customer, select his orders and push to Odoo */
            /** @var \Magento\Framework\Api\SearchCriteria $crit */
            $crit = $this->manObj->create(\Magento\Framework\Api\SearchCriteria::class);
            /** @var \Magento\Framework\Api\Search\FilterGroup $filterGroup */
            $filterGroup = $this->manObj->create(\Magento\Framework\Api\Search\FilterGroup::class);
            $filterGroup->setFilters();
            /** @var \Magento\Framework\Api\Filter $filter */
            $filter = $this->manObj->create(\Magento\Framework\Api\Filter::class);
            $filter->setField(\Magento\Sales\Api\Data\OrderInterface::CUSTOMER_ID);
            $filter->setValue($idAfter);
            $filterGroup->setFilters([$filter]);
            $crit->setFilterGroups([$filterGroup]);
            $orders = $this->repoOrder->getList($crit);
            foreach ($orders as $order) {
                $req = new \Praxigento\Odoo\Service\Replicate\Request\OrderSave();
                $req->setSaleOrder($order);
                $this->callReplicate->orderSave($req);
            }
        }
        return;
    }
}