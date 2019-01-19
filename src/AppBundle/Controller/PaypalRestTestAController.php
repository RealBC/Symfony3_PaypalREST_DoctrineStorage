<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Payum\Core\Request\GetHumanStatus;
use Payum\Core\Model\PaymentARest;



use AppBundle\Payum\PaypalRestDoctrine\Action;

use Symfony\Component\HttpFoundation\JsonResponse;


class PaypalRestTestAController extends Controller
{
    

    /**
     * @Route("/paypal_a", name="paypal_a")
     */
    public function prepareAction() 
    {
        $gatewayName = 'paypal_rest_doctrine';
        
        $storage = $this->get('payum')->getStorage('AppBundle\Entity\PaymentARest');
        $details= $storage->create();
       
        $details->setIdStorage(uniqid());
        $details['method']='paypal_rest_doctrine';
        $details['customerFirstName']='ben';
        $details['customerLastName']='co';
        $itemList=[
                        ['name'=>'café','quantity'=>1,'sku'=>'123123','price'=>7.5],
                        ['name'=>'gâteaux','quantity'=>5,'sku'=>'321321','price'=>2]
        ];

        $details['transactions']=[
                                    'itemList'=>$itemList,
                                    'currency'=>'USD',
                                    'description'=>'Alleluia',
                                    'invoiceNumber'=>uniqid()
                                ];
   
        $storage->update($details);

        $captureToken = $this->get('payum')->getTokenFactory()->createCaptureToken(
            $gatewayName, 
            $details, 
            'done_paypal_rest_doctrine' // the route to redirect after capture
        );

        $storage->findBy(array('idStorage'=>uniqid()));
        $details['redirectUrls']=['returnUrl'=>$captureToken->getTargetUrl(),'cancelUrl'=>$captureToken->getTargetUrl()];
        $details['intent']='sale';

        $storage->update($details);

        return $this->redirect($captureToken->getTargetUrl()); 
    }


    /**
     * @Route("/paypal/done_paypal", name="done_paypal_rest_doctrine")
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
        $status = new GetHumanStatus($token);
      
        
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
