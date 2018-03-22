<?php
/**
 * Populate DB schema with module's initial data
 * .
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Setup;

use Praxigento\Accounting\Repo\Data\Type\Operation as TypeOperation;
use Praxigento\Odoo\Config as Cfg;

/**
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class InstallData extends \Praxigento\Core\App\Setup\Data\Base
{
    protected function _setup()
    {
        $this->addAccountingOperationsTypes();
    }

    private function addAccountingOperationsTypes()
    {
        $this->_conn->insertArray(
            $this->_resource->getTableName(TypeOperation::ENTITY_NAME),
            [TypeOperation::ATTR_CODE, TypeOperation::ATTR_NOTE],
            [
                [
                    Cfg::CODE_TYPE_OPER_WALLET_DEBIT,
                    'Debit WALLET_ACTIVE asset from customer account (consignments).'
                ]
            ]
        );
    }
}