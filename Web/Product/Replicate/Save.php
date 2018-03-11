<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Web\Product\Replicate;

class Save
    implements \Praxigento\Odoo\Api\Web\Product\Replicate\SaveInterface
{
    /** @var \Praxigento\Odoo\Service\Replicate\Product\Save */
    private $servSave;

    public function __construct(
        \Praxigento\Odoo\Service\Replicate\Product\Save $servSave
    ) {
        $this->servSave = $servSave;
    }

    public function exec($data)
    {
        $req = new \Praxigento\Odoo\Service\Replicate\Product\Save\Request();
        $req->setInventory($data);
        $this->servSave->exec($req);
        /* return true if no errors occurred */
        $result = true;
        return $result;
    }

}