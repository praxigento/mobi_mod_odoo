<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Api\Product\Replicate;

class Save
    implements \Praxigento\Odoo\Api\Product\Replicate\SaveInterface
{
    /** @var \Praxigento\Odoo\Service\Replicate\Product\Save */
    private $servSave;

    public function __construct(
        \Praxigento\Odoo\Service\Replicate\Product\Save $servSave
    ) {
        $this->servSave = $servSave;
    }

    public function execute(\Praxigento\Odoo\Data\Odoo\Inventory $data)
    {
        $req = new \Praxigento\Odoo\Service\Replicate\Product\Save\Request();
        $req->setInventory($data);
        $this->servSave->exec($req);
        /* return true if no errors occurred */
        $result = true;
        return $result;
    }

}