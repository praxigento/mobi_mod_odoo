<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Repo\Entity\Def;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class Category_UnitTest extends \Praxigento\Core\Lib\Test\BaseMockeryCase
{
    /** @var  \Mockery\MockInterface */
    private $mRepoBasic;
    /** @var  Category */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /* create mocks */
        $this->mRepoBasic = $this->_mockRepoBasic();
        /* setup mocks for constructor */
        /* create object to test */
        $this->obj = new Category(
            $this->mRepoBasic
        );
    }

    public function test_constructor()
    {
        /* === Test Data === */
        /* === Setup Mocks === */
        /* === Call and asserts  === */
        $this->assertInstanceOf(Category::class, $this->obj);
    }

}