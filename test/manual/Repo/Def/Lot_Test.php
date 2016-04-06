<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Lib\Repo\Def;

use Magento\Framework\App\ObjectManager;
use Praxigento\Odoo\Data\Agg\Lot as AggLot;
use Praxigento\Odoo\Lib\Repo\ILot;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Lot_ManualTest extends \Praxigento\Core\Lib\Test\BaseIntegrationTest
{

    public function test_checkExistence()
    {
        $obm = ObjectManager::getInstance();
        /** @var  $repo ILot */
        $repo = $obm->get(ILot::class);
        /** @var  $data AggLot */
        $data = $obm->create(AggLot::class);
        $data->setOdooId(21);
        $data->setCode('manual test');
        /* check existence of the lot and register new one if required */
        $created = $repo->checkExistence($data);
        return;
    }

}