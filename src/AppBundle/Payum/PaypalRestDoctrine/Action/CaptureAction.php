<?php

namespace AppBundle\Payum\PaypalRestDoctrine\Action;

use AppBundle\Entity\PaymentARest;

use PayPal\Api\Payment as PaypalPayment;
use PayPal\Api\PaymentExecution;
use PayPal\Rest\ApiContext;
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\ApiAwareTrait;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\Capture;

use PayPal\Api\Address;
use PayPal\Api\Payer;
use PayPal\Api\PayerInfo;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Amount;
use PayPal\Api\Transaction;
use PayPal\Api\RedirectUrls;
use Payum\Paypal\Rest\Model\PaymentDetails;


use Payum\Core\Reply\HttpRedirect;

class CaptureAction implements ActionInterface, GatewayAwareInterface, ApiAwareInterface
{
    use ApiAwareTrait;
    use GatewayAwareTrait;

    public function __construct()
    {
        $this->apiClass = ApiContext::class;
    }

    /**
     * {@inheritDoc}
     */
    public function execute($request)
    {
        //dump('hello capture doctrine paypal rest'); die();
        /** @var $request Capture */
        RequestNotSupportedException::assertSupports($this, $request);

        $model = $request->getModel();

        if(!isset($model['state']))
        {

            $flowConfig = new \PayPal\Api\FlowConfig();
            $flowConfig->setLandingPageType("Billing")
                        ->setBankTxnPendingUrl("https://www.latelierdemaguy.fr/")
                        ->setUserAction("commit")
                        ->setReturnUriHttpMethod("GET");
            
            $presentation = new \PayPal\Api\Presentation();
            $presentation->setLogoImage("https://latelierdemaguy.fr/bundles/_themes/custom/custom-theme/syliusshop/img-shop/favicon.ico")
                        ->setBrandName("L'Atelier de Maguy")
                        ->setLocaleCode("FR")
                        ->setReturnUrlLabel("Return")
                        ->setNoteToSellerLabel("Thanks!");

            $inputFields = new \PayPal\Api\InputFields();
            $inputFields->setAllowNote(true)
                        ->setNoShipping(1)
                        ->setAddressOverride(0);

            $webProfile = new \PayPal\Api\WebProfile();
            $webProfile->setName("YIHAAAA" . uniqid())
                        ->setFlowConfig($flowConfig)
                        ->setPresentation($presentation)
                        ->setInputFields($inputFields)
                        ->setTemporary(true);


            $address = new Address();
            $address->setLine1('17 rue du pont')
                    ->setCity('la rochelle')
                    ->setPostalCode('17000')
                    ->setCountryCode('FR');
                   

            $info = new PayerInfo();
            $info->setFirstName($model['customerFirstName'])
                ->setLastName($model['customerLastName'])
                ->setEmail('b@b.com')
                
                ->setBillingAddress($address);
                //->setCountryCode('FR');

            $payer = new Payer();
            $payer->setPaymentMethod('paypal')
                    ->setPayerInfo($info);

            $sum=0;
            foreach($model['transactions']['itemList'] as $i)
            {
                $itemObj = new Item();
                $itemObj->setName($i['name'])
                        ->setCurrency($model['transactions']['currency'])
                        ->setQuantity($i['quantity'])
                        ->setSku($i['sku']) // Similar to `item_number` in Classic API
                        ->setPrice($i['price']);
                $itemArray[]=$itemObj ;

                $sum+=($i['price']*$i['quantity']);
            }


            $itemList = new ItemList();
            $itemList->setItems($itemArray);

            $amount = new Amount();
            $amount->setCurrency($model['transactions']['currency']);
            $amount->setTotal($sum);

            $transaction = new Transaction();
            $transaction->setItemList($itemList);
            $transaction->setAmount($amount);
            $transaction->setDescription($model['transactions']['description']);
            $transaction->setInvoiceNumber(uniqid());

            $redirectUrls = new RedirectUrls();
            $redirectUrls->setReturnUrl($model['redirectUrls']['returnUrl']);
            $redirectUrls->setCancelUrl($model['redirectUrls']['cancelUrl']);

            $submodel= new PaymentDetails();
            $submodel->setIntent('sale');
            $submodel->setPayer($payer);
            $submodel->setRedirectUrls($redirectUrls);
            $submodel->setTransactions(array($transaction));
            $submodel->setExperienceProfileId($webProfile->create($this->api)->id);

            $submodel->create($this->api);
            
            $model['id']=$submodel->id;
            $model['state']=$submodel->state;
            $model['create_time']=$submodel->create_time;
             
            foreach ($submodel->links as $link) {
                if ($link->rel == 'approval_url') {

                    throw new HttpRedirect($link->href);
                }
            }
        }

        if(isset($model['state']) && isset($_GET['PayerID']))
        {
            
            $payment = PaypalPayment::get($model['id'], $this->api);

            $execution = new PaymentExecution();
            $execution->payer_id = $_GET['PayerID'];

            //Execute the payment
            try{
                $payment->execute($execution, $this->api);
            }
            catch(Exception $e){
                throw new Exception('Failed to take payment'); 
            }

            $model['id']=$payment->id;
            $model['state']=$payment->state;
            $model['create_time']=$payment->create_time;
            $model['cart']=$payment->cart;
            
        }
        
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Capture &&
            //$request->getModel() instanceof PaymentDetails
            $request->getModel() instanceof PaypalPayment ||
            $request->getModel() instanceof PaymentARest
            
        ;
    }
}
