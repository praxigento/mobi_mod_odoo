<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Query\Replicate\Sale\Orders\Get;

use Magento\Framework\App\ObjectManager as ObjMan;

include_once(__DIR__ . '/../../../../../../phpunit_bootstrap.php');

class Builder_ManualTest
    extends \Praxigento\Core\Test\BaseCase\Mockery
{

    /** @var  \Praxigento\Odoo\Repo\Query\Replicate\Sale\Orders\Get\Builder */
    private $obj;

    public function setUp()
    {
        parent::setUp();
        $this->obj = ObjMan::getInstance()->create(\Praxigento\Odoo\Repo\Query\Replicate\Sale\Orders\Get\Builder::class);
    }

    public function test_getIdsToSaveToOdoo()
    {
        $res = $this->obj->build();
    }


}