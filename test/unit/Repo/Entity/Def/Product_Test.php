<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Repo\Entity\Def;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class Product_UnitTest extends \Praxigento\Core\Lib\Test\BaseMockeryCase
{
    /** @var  \Mockery\MockInterface */
    private $mRepoBasic;
    /** @var  Product */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /* create mocks */
        $this->mRepoBasic = $this->_mockRepoBasic();
        /* setup mocks for constructor */
        /* create object to test */
        $this->obj = new Product(
            $this->mRepoBasic
        );
    }

    public function test_constructor()
    {
        /* === Test Data === */
        /* === Setup Mocks === */
        /* === Call and asserts  === */
        $this->assertInstanceOf(Product::class, $this->obj);
    }

}