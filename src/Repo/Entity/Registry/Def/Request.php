<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Repo\Entity\Registry\Def;

class Request
    extends \Praxigento\Core\Repo\Def\Entity
    implements \Praxigento\Odoo\Repo\Entity\Registry\IRequest
{
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Core\Repo\IGeneric $repoGeneric
    ) {
        parent::__construct($resource, $repoGeneric, \Praxigento\Odoo\Data\Entity\Registry\Request::class);
    }
}