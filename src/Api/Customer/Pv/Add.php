<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Api\Customer\Pv;


class Add
    implements \Praxigento\Odoo\Api\Customer\Pv\AddInterface
{
    const ODOO_REF_TYPE_CODE = \Praxigento\Odoo\Helper\Code\Request::CUSTOMER_PV_ADD;
    /** @var \Praxigento\Downline\Repo\Entity\ICustomer */
    protected $repoCustomer;
    /** @var \Praxigento\Odoo\Repo\Entity\Registry\IRequest */
    protected $repoRegRequest;
    protected $callPvTransfer;

    public function __construct(
        \Praxigento\Downline\Repo\Entity\ICustomer $repoCustomer,
        \Praxigento\Odoo\Repo\Entity\Registry\IRequest $repoRegRequest,
        \Praxigento\Pv\Service\ITransfer $callPvTransfer
    ) {
        $this->repoCustomer = $repoCustomer;
        $this->repoRegRequest = $repoRegRequest;
        $this->callPvTransfer = $callPvTransfer;
    }

    public function execute(\Praxigento\Odoo\Api\Data\Customer\Pv\Add\Request $data)
    {
        $result = new \Praxigento\Odoo\Api\Data\Customer\Pv\Add\Response();
        /* parse request data */
        $customerMlmId = $data->getCustomerMlmId();
        $pv = $data->getPv();
        $dateApplied = $data->getDateApplied();
        $odooRef = $data->getOdooRef();
        /* process request data */
        /* prevent duplication */
        $key = [
            \Praxigento\Odoo\Data\Entity\Registry\Request::ATTR_TYPE_CODE => self::ODOO_REF_TYPE_CODE,
            \Praxigento\Odoo\Data\Entity\Registry\Request::ATTR_ODOO_REF => $odooRef
        ];
        $found = $this->repoRegRequest->getById($key);
        if ($found) {
            throw new \Exception ("Odoo request referenced as '$odooRef' is already processed.");
        }
        /* find customer by MLM ID */
        $customer = $this->repoCustomer->getByMlmId($customerMlmId);
        if (!$customer) {
            throw new \Exception ("Customer #$customerMlmId is not found.");
        }
        /* add PV to account */
        $req = new \Praxigento\Pv\Service\Transfer\Request\CreditToCustomer();
        $req->setToCustomerId($customer->getCustomerId());
        $req->setValue($pv);
        $req->setDateApplied($dateApplied);
        $note = "PV is added from Odoo (ref.#$odooRef).";
        $req->setNoteOperation($note);
        $req->setNoteTransaction($note);
        $resp = $this->callPvTransfer->creditToCustomer($req);
        if ($resp->isSucceed()) {
            $this->repoRegRequest->create($key);
            /* compose response */
            $result->setOdooRef($odooRef);
            $result->setOperationId($resp->getOperationId());
            $transIds = $resp->getTransactionsIds();
            $oneId = reset($transIds);
            $result->setTransactionId($oneId);
        }
        return $result;
    }

}