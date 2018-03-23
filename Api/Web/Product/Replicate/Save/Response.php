<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Api\Web\Product\Replicate\Save;

/**
 * Save product inventory data to Magento (push replication).
 */
class Response
    extends \Praxigento\Core\Api\App\Web\Response
{
    /**
     * @return \Praxigento\Odoo\Api\Web\Product\Replicate\Save\Response\Data|null
     */
    public function getData()
    {
        $result = parent::get(self::A_DATA);
        return $result;
    }

    /**
     * @param \Praxigento\Odoo\Api\Web\Product\Replicate\Save\Response\Data $data
     */
    public function setData($data)
    {
        parent::set(self::A_DATA, $data);
    }

}