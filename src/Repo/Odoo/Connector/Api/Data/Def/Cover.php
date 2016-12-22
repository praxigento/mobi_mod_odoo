<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Odoo\Connector\Api\Data\Def;

use Flancer32\Lib\DataObject;
use Praxigento\Odoo\Repo\Odoo\Connector\Api\Data\ICover;

class Cover extends DataObject implements ICover
{
    const PATH_RESULT_DATA = '/result';

    /**
     * Define constructor explicitly to use with Object Manager (init data on construct).
     *
     * @param $data
     */
    public function __construct($data)
    {
        parent::__construct($data);
    }

    public function getResultData()
    {
        $result = parent::get(self::PATH_RESULT_DATA);
        return $result;
    }

}