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
use Praxigento\Core\Repo\Query\Criteria\IAdapter as ICriteriaAdapter;
use Praxigento\Core\Ui\DataProvider\Base as BaseDataProvider;
use Praxigento\Odoo\Repo\Agg\Store\ILot as IRepoAggLot;

class Lot extends BaseDataProvider
{

    /**
     * @inheritdoc
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        UrlInterface $url,
        ICriteriaAdapter $critAdapter,
        IRepoAggLot $repo,
        Reporting $reporting,
        SearchCriteriaBuilder $searchCritBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        $name,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $url,
            $critAdapter,
            null,
            $repo,
            $reporting,
            $searchCritBuilder,
            $request,
            $filterBuilder,
            $name,
            $meta,
            $data
        );
    }
}