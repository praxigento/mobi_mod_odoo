<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Ui\DataProvider;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\Reporting;
use Magento\Store\Model\StoreManagerInterface;
use Praxigento\Odoo\Repo\Agg\ILot as IRepoAggLot;

class Lot extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{

    const JSON_ATTR_ITEMS = 'items';
    const JSON_ATTR_TOTAL_RECORDS = 'totalRecords';

    /**#@+
     * UI XML arguments and default values to configure this component.
     */
    const UICD_UPDATE_URL = 'mui/index/render';
    const UIC_CONFIG = 'config';
    const UIC_UPDATE_URL = 'update_url';
    /**#@-*/

    /** @var  IRepoAggLot */
    protected $_repo;

    /**
     * Warehouse constructor.
     * @param UrlInterface $url
     * @param IRepoAggLot $repo
     * @param Reporting $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param FilterBuilder $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        UrlInterface $url,
        IRepoAggLot $repo,
        Reporting $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        $name,
        $primaryFieldName = 'entity_id',
        $requestFieldName = 'id',
        array $meta = [],
        array $data = []
    ) {
        /* add default Update URL */
        if (!isset($data[static::UIC_CONFIG][static::UIC_UPDATE_URL])) {
            $val = $url->getRouteUrl(static::UICD_UPDATE_URL);
            $data[static::UIC_CONFIG][static::UIC_UPDATE_URL] = $val;
        }
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );
        /* post construction setup */
        $this->_repo = $repo;
    }

    public function getData()
    {
        $criteria = $this->getSearchCriteria();
        $pageSize = $criteria->getPageSize();
        $pageIndx = $criteria->getCurrentPage();
        $where = null;
        $order = null;
        /** @var \Magento\Framework\DB\Select $queryTotal */
        $queryTotal = $this->_repo->getQueryToSelectCount();
        $total = $queryTotal->getConnection()->fetchOne($queryTotal);
        /** @var \Magento\Framework\DB\Select $query */
        $query = $this->_repo->getQueryToSelect();
        $query->limitPage($pageIndx, $pageSize);
        $data = $query->getConnection()->fetchAll($query);
        $result = [
            static::JSON_ATTR_TOTAL_RECORDS => $total,
            static::JSON_ATTR_ITEMS => $data
        ];
        return $result;
    }
}