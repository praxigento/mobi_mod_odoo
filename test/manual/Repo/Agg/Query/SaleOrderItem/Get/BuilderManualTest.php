<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Agg\Query\SaleOrderItem\Get;

include_once(__DIR__ . '/../../../../../phpunit_bootstrap.php');

class BuilderManualTest
    extends \Praxigento\Core\Test\BaseCase\Manual
{

    /** @var  \Praxigento\Odoo\Repo\Agg\Query\SaleOrderItem\Get\Builder */
    private $obj;

    public function setUp()
    {
        parent::setUp();
        $this->obj = $this->manObj->create(\Praxigento\Odoo\Repo\Agg\Query\SaleOrderItem\Get\Builder::class);
    }

    public function test_build()
    {
        $res = $this->obj->build();
    }

}