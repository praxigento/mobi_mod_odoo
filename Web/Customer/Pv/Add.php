<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Api\Web\Customer\Pv;
class Add
    implements \Praxigento\Odoo\Api\Web\Customer\Pv\AddInterface
{
    const ODOO_REF_TYPE_CODE = \Praxigento\Odoo\Helper\Code\Request::CUSTOMER_PV_ADD;
    /** @var \Praxigento\Odoo\Api\App\Logger\Main */
    private $logger;
    /** @var \Praxigento\Downline\Repo\Entity\Customer */
    private $repoCustomer;
    /** @var \Praxigento\Odoo\Repo\Entity\Registry\Request */
    private $repoRegRequest;
    /** @var \Praxigento\Pv\Service\ITransfer */
    private $servPvTransfer;

    public function __construct(
        \Praxigento\Odoo\Api\App\Logger\Main $logger,
        \Praxigento\Downline\Repo\Entity\Customer $repoCustomer,
        \Praxigento\Odoo\Repo\Entity\Registry\Request $repoRegRequest,
        \Praxigento\Pv\Service\ITransfer $servPvTransfer
    ) {
        $this->logger = $logger;
        $this->repoCustomer = $repoCustomer;
        $this->repoRegRequest = $repoRegRequest;
        $this->servPvTransfer = $servPvTransfer;
    }

    public function exec($data)
    {
        assert($data instanceof \Praxigento\Odoo\Api\Web\Customer\Pv\Add\Request);
        $result = new \Praxigento\Odoo\Api\Web\Customer\Pv\Add\Response();
        /* parse request data */
        $customerMlmId = $data->getCustomerMlmId();
        $pv = $data->getPv();
        $dateApplied = $data->getDateApplied();
        $odooRef = $data->getOdooRef();
        /* process request data */
        /* prevent duplication */
        $key = [
            \Praxigento\Odoo\Repo\Entity\Data\Registry\Request::ATTR_TYPE_CODE => self::ODOO_REF_TYPE_CODE,
            \Praxigento\Odoo\Repo\Entity\Data\Registry\Request::ATTR_ODOO_REF => $odooRef
        ];
        $found = $this->repoRegRequest->getById($key);
        if ($found) {
            $msg = "Odoo request referenced as '$odooRef' is already processed.";
            $this->logger->error($msg);
            $result->getResult()->setCode($result::CODE_DUPLICATED);
            $result->getResult()->setText($msg);
        } else {
            /* find customer by MLM ID */
            $customer = $this->repoCustomer->getByMlmId($customerMlmId);
            if (!$customer) {
                $msg = "Customer #$customerMlmId is not found.";
                $this->logger->error($msg);
                $result->getResult()->setCode($result::CODE_CUSTOMER_IS_NOT_FOUND);
                $result->getResult()->setText($msg);
            } else {
                try {
                    /* add PV to account */
                    $req = new \Praxigento\Pv\Service\Transfer\Request\CreditToCustomer();
                    $req->setToCustomerId($customer->getCustomerId());
                    $req->setValue($pv);
                    $req->setDateApplied($dateApplied);
                    $note = "PV is added from Odoo (ref. #$odooRef).";
                    $req->setNoteOperation($note);
                    $req->setNoteTransaction($note);
                    $resp = $this->servPvTransfer->creditToCustomer($req);
                    if ($resp->isSucceed()) {
                        $this->repoRegRequest->create($key);
                        /* compose response */
                        $respData = new \Praxigento\Odoo\Api\Web\Customer\Pv\Add\Response\Data();
                        $respData->setOdooRef($odooRef);
                        $respData->setOperationId($resp->getOperationId());
                        $transIds = $resp->getTransactionsIds();
                        $oneId = reset($transIds);
                        $respData->setTransactionId($oneId);
                        $result->setData($respData);
                        $result->getResult()->setCode($result::CODE_SUCCESS);
                        $msg = "$pv PV are credit to customer #$customerMlmId (odoo ref. #$odooRef).";
                        $this->logger->info($msg);
                    }
                } catch (\Throwable $e) {
                    $msg = $e->getMessage();
                    $msg .= "\n" . $e->getTraceAsString();
                    $result->getResult()->setText($msg);
                }
            }
        }
        return $result;
    }

}