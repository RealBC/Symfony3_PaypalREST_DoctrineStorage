<?php

namespace AppBundle\EventListener;

use Payum\Core\Bridge\Symfony\Event\ExecuteEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\EventDispatcher\Event;

use Payum\Paypal\Rest\Model\PaymentDetails;
use AppBundle\Paypal\PaymentARest;

class PaypalRestConvertor
{
    public function onPayumGatewayPostExecute(ExecuteEvent $event)
    {
    
        dump('ON Gateway Execute');
        dump($event);
        
    }
}