<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Payum\Core\Request\GetHumanStatus;
use Payum\Core\Model\Payment;
use Payum\Core\Storage\FilesystemStorage;
use Symfony\Component\HttpFoundation\JsonResponse;
use Payum\Paypal\Rest\Model\PaymentDetails;

use PayPal\Api\Amount;
use PayPal\Api\Payer;
use PayPal\Api\PayerInfo;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Api\Item;
use PayPal\Api\ItemList;

class PaypalFSController extends Controller
{
    /**
     * @Route("/paypalFs", name="paypal_fs")
     */
    public function prepareAction() 
    {
        $gatewayName = 'paypal_rest';

        //$storage = new FilesystemStorage($this->get('kernel')->getRootDir() . '/../app/Resources/payments', 'Payum\Paypal\Rest\Model\PaymentDetails', 'idStorage');

        $storage = $this->get('payum')->getStorage(PaymentDetails::class);

        $payment = $storage->create();
        $storage->update($payment);
       

        $payer = new Payer();
        $payer->setPaymentMethod('paypal');
        
        $item1 = new Item();
        $item1->setName('Ground Coffee 40 oz')
            ->setCurrency('USD')
            ->setQuantity(1)
            ->setSku("123123") // Similar to `item_number` in Classic API
            ->setPrice(7.5);

        $item2 = new Item();
        $item2->setName('Granola bars')
            ->setCurrency('USD')
            ->setQuantity(5)
            ->setSku("321321") // Similar to `item_number` in Classic API
            ->setPrice(2);

        $itemList = new ItemList();
        $itemList->setItems(array($item1, $item2));


        $amount = new Amount();
        $amount->setCurrency('USD');
        $amount->setTotal(17.5);

        $transaction = new Transaction();
        $transaction->setItemList($itemList);
        $transaction->setAmount($amount);
        $transaction->setDescription('Alleluia');
        $transaction->setInvoiceNumber(uniqid());

        
        $captureToken = $this->get('payum')->getTokenFactory()->createCaptureToken(
            $gatewayName, 
            $payment, 
            'done_paypal_fs_rest' // the route to redirect after capture
        );

        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl($captureToken->getTargetUrl());
        $redirectUrls->setCancelUrl($captureToken->getTargetUrl());

        $payment->setIntent('sale');
        $payment->setPayer($payer);
        $payment->setRedirectUrls($redirectUrls);
        $payment->setTransactions(array($transaction));
        
        $storage->update($payment);
 
        return $this->redirect($captureToken->getTargetUrl()); 
    }


    /**
     * @Route("/paypalFS/done_paypal", name="done_paypal_fs_rest")
     */
    public function doneAction(Request $request)
    {
        $token = $this->get('payum')->getHttpRequestVerifier()->verify($request);
        
        $gateway = $this->get('payum')->getGateway($token->getGatewayName());
        
        // You can invalidate the token, so that the URL cannot be requested any more:
        // $this->get('payum')->getHttpRequestVerifier()->invalidate($token);
        
        // Once you have the token, you can get the payment entity from the storage directly. 
        // $identity = $token->getDetails();
        // $payment = $this->get('payum')->getStorage($identity->getClass())->find($identity);
        
        // Or Payum can fetch the entity for you while executing a request (preferred).
        $gateway->execute($status = new GetHumanStatus($token));
        $payment = $status->getFirstModel();
        
        // Now you have order and payment status
        
        /*return new JsonResponse(array(
            'status' => $status->getValue(),
            'token'  => $token->getHash(),
            'payment' => array(
                'total_amount' => 123,//$payment->getTotalAmount(),
                'currency_code' => 'EUR', //$payment->getCurrencyCode(),
                'details' => '', //$payment->getDetails(),
            ),
        ));*/
        return $this->render('@App/payment_done.html.twig',array(
            'status' => $status->getValue(),
            'token'  => $token->getHash(),
            'payment' => (array)$payment,
            )
        );
    }
}
