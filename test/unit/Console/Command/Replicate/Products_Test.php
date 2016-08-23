<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Console\Command\Replicate;

use Praxigento\Odoo\Service\Replicate\Request\ProductsFromOdoo as ProductsFromOdooRequest;
use Praxigento\Odoo\Service\Replicate\Response\ProductsFromOdoo as ProductsFromOdooResponse;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

/**
 * For tests only.
 */
class ProductsChild extends Products
{

    public function launchExecute($input, $output)
    {
        $this->execute($input, $output);
    }
}

class Products_UnitTest extends \Praxigento\Core\Test\BaseCase\Mockery
{
    /** @var  \Mockery\MockInterface */
    private $mCallOdooReplicate;
    /** @var  \Mockery\MockInterface */
    private $mInput;
    /** @var  \Mockery\MockInterface */
    private $mManObj;
    /** @var  \Mockery\MockInterface */
    private $mOutput;
    /** @var  ProductsChild */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mManObj = $this->_mockObjectManager();
        $this->mCallOdooReplicate = $this->_mock(\Praxigento\Odoo\Service\IReplicate::class);
        /* parameters */
        $this->mInput = $this->_mock(InputInterface::class);
        $this->mOutput = $this->_mock(OutputInterface::class);
        /** create object to test */
        $this->obj = new ProductsChild(
            $this->mManObj,
            $this->mCallOdooReplicate
        );
    }

    public function test_execute_withIds_done()
    {
        /** === Test Data === */
        $IDS = '1,2,3';
        $CONFIG = ['config'];
        /** === Setup Mocks === */
        // $argIds = $input->getArgument(static::ARG_IDS);
        $this->mInput
            ->shouldReceive('getArgument')->once()
            ->andReturn($IDS);
        // $output->writeln('<info>List of all products will be pulled from Odoo.<info>');
        $this->mOutput
            ->shouldReceive('writeln')->once();
        // $this->_setAreaCode();
        // $appState = $this->_manObj->get(\Magento\Framework\App\State::class);
        $mAppState = $this->_mock(\Magento\Framework\App\State::class);
        $this->mManObj
            ->shouldReceive('get')->once()
            ->andReturn($mAppState);
        // $appState->setAreaCode($areaCode);
        $mAppState->shouldReceive('setAreaCode')->once();
        // $configLoader = $this->_manObj->get(\Magento\Framework\ObjectManager\ConfigLoaderInterface::class);
        $mConfigLoader = $this->_mock(\Magento\Framework\ObjectManager\ConfigLoaderInterface::class);
        $this->mManObj
            ->shouldReceive('get')->once()
            ->andReturn($mConfigLoader);
        // $config = $configLoader->load($areaCode);
        $mConfigLoader->shouldReceive('load')->once()
            ->andReturn($CONFIG);
        // $this->_manObj->configure($config);
        $this->mManObj
            ->shouldReceive('configure')->once();
        // return
        // $req = $this->_manObj->create(ProductsFromOdooRequest::class);
        $this->mManObj
            ->shouldReceive('create')->once()
            ->andReturn(new ProductsFromOdooRequest());
        // resp = $this->_callReplicate->productsFromOdoo($req);
        $mResp = new ProductsFromOdooResponse();
        $this->mCallOdooReplicate
            ->shouldReceive('productsFromOdoo')->once()
            ->andReturn($mResp);
        // if ($resp->isSucceed()) {...}
        $mResp->markSucceed();
        // $output->writeln('<info>Replication is done.<info>');
        $this->mOutput
            ->shouldReceive('writeln')->once();
        /** === Call and asserts  === */
        $this->obj->launchExecute($this->mInput, $this->mOutput);
    }

    public function test_execute_woIds_failed()
    {
        /** === Test Data === */
        $CONFIG = ['config'];
        /** === Setup Mocks === */
        // $argIds = $input->getArgument(static::ARG_IDS);
        $this->mInput
            ->shouldReceive('getArgument')->once()
            ->andReturn(null);
        // $output->writeln('<info>List of all products will be pulled from Odoo.<info>');
        $this->mOutput
            ->shouldReceive('writeln')->once();
        // $this->_setAreaCode();
        // $appState = $this->_manObj->get(\Magento\Framework\App\State::class);
        $mAppState = $this->_mock(\Magento\Framework\App\State::class);
        $this->mManObj
            ->shouldReceive('get')->once()
            ->andReturn($mAppState);
        // $appState->setAreaCode($areaCode);
        $mAppState->shouldReceive('setAreaCode')->once();
        // $configLoader = $this->_manObj->get(\Magento\Framework\ObjectManager\ConfigLoaderInterface::class);
        $mConfigLoader = $this->_mock(\Magento\Framework\ObjectManager\ConfigLoaderInterface::class);
        $this->mManObj
            ->shouldReceive('get')->once()
            ->andReturn($mConfigLoader);
        // $config = $configLoader->load($areaCode);
        $mConfigLoader->shouldReceive('load')->once()
            ->andReturn($CONFIG);
        // $this->_manObj->configure($config);
        $this->mManObj
            ->shouldReceive('configure')->once();
        // return
        // $req = $this->_manObj->create(ProductsFromOdooRequest::class);
        $this->mManObj
            ->shouldReceive('create')->once()
            ->andReturn(new ProductsFromOdooRequest());
        // resp = $this->_callReplicate->productsFromOdoo($req);
        $mResp = new ProductsFromOdooResponse();
        $this->mCallOdooReplicate
            ->shouldReceive('productsFromOdoo')->once()
            ->andReturn($mResp);
        // $output->writeln('<info>Replication is failed.<info>');
        $this->mOutput
            ->shouldReceive('writeln')->once();
        /** === Call and asserts  === */
        $this->obj->launchExecute($this->mInput, $this->mOutput);
    }

}