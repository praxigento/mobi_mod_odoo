<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Lib\Repo\Entity\Def;


use Magento\Framework\App\ObjectManager;
use Praxigento\Odoo\Repo\Agg\Data\Warehouse as AggWarehouse;
use Praxigento\Odoo\Repo\Agg\IWarehouse;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class Warehouse_ManualTest extends \Praxigento\Core\Test\BaseIntegrationTest
{


    public function test_create()
    {
        $obm = ObjectManager::getInstance();
        /** @var  $repo IWarehouse */
        $repo = $obm->get(IWarehouse::class);
        /** @var  $data AggWarehouse */
        $data = $obm->create(AggWarehouse::class);
        $data->setWebsiteId(self::DEF_WEBSITE_ID_BASE);
        $data->setCode('TEST STOCK 3');
        $data->setNote('Сделано из теста');
        $data->setOdooId(24);
        $data->setCurrency('EUR');
        $created = $repo->create($data);
        return;
    }

    public function test_getById()
    {
        $obm = Ctx::instance()->getObjectManager();
        /** @var  $repo IWarehouse */
        $repo = $obm->get(IWarehouse::class);
        $data = $repo->getById(1);
        return;
    }

}