<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Odoo\Plugin\Magento\Backend\Block\Widget\Grid;

class Extended
{
    public function beforeSetCollection(
        \Magento\Backend\Block\Widget\Grid\Extended $subject,
        \Magento\Framework\Data\Collection $collection
    ) {
        /* MOBI-1542: prevent "Column 'customer_id' in where clause is ambiguous" */
        if ($collection instanceof \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection) {
            /**
             * @var \Magento\Framework\DB\Select $query
             */
            $query = $collection->getSelect();
            if ($query) {
                $where = $query->getPart(\Magento\Framework\DB\Select::WHERE);
                foreach ($where as $key => $one) {
                    $replaced = str_replace('(`customer_id`', '(`main_table`.`customer_id`', $one);
                    $where[$key] = $replaced;
                }
                $query->setPart(\Magento\Framework\DB\Select::WHERE, $where);
            }
        }
        return [$collection];
    }
}