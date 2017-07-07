<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Query\Replicate\Sale\Orders\Items\Lots\Get;


include_once(__DIR__ . '/../../../../../../../../phpunit_bootstrap.php');

class Builder_Test
    extends \Praxigento\Core\Test\BaseCase\Manual
{
    public function test_build()
    {
        /** @var \Praxigento\Odoo\Repo\Query\Replicate\Sale\Orders\Items\Lots\Get\Builder $obj */
        $obj = $this->manObj->create(\Praxigento\Odoo\Repo\Query\Replicate\Sale\Orders\Items\Lots\Get\Builder::class);
        $res = $obj->build();
        $this->assertNotNull($res);
    }

}