<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Api\Def;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class ProductReplicator_UnitTest extends \Praxigento\Core\Test\BaseMockeryCase
{
    /** @var  \Mockery\MockInterface */
    private $mCallOdooReplicate;
    /** @var  \Mockery\MockInterface */
    private $mManObj;
    /** @var  ProductReplicator */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mManObj = $this->_mockObjectManager();
        $this->mCallOdooReplicate = $this->_mock(\Praxigento\Odoo\Service\IReplicate::class);
        /** setup mocks for constructor */
        /** create object to test */
        $this->obj = new ProductReplicator(
            $this->mManObj,
            $this->mCallOdooReplicate
        );
    }

    public function test_save()
    {
        /** === Test Data === */
        $DATA = new \Praxigento\Odoo\Data\Odoo\Inventory();
        /** === Setup Mocks === */
        // $req = $this->manObj->create(\Praxigento\Odoo\Service\Replicate\Request\ProductSave::class);
        $this->mManObj
            ->shouldReceive('create')->once()
            ->andReturn(new \Praxigento\Odoo\Service\Replicate\Request\ProductSave());
        // $resp = $this->_callOdooReplicate->productSave($req);
        $this->mCallOdooReplicate
            ->shouldReceive('productSave')->once();
        /** === Call and asserts  === */
        $this->obj->save($DATA);
    }

}