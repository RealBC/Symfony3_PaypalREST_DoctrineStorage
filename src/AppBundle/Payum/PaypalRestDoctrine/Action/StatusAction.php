<?php

namespace AppBundle\Payum\PaypalRestDoctrine\Action;

use AppBundle\Entity\PaymentARest;
use PayPal\Api\Payment;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\GetStatusInterface;

class StatusAction implements ActionInterface
{
    /**
     * {@inheritDoc}
     *
     * @var GetStatusInterface $request
     */
    public function execute($request)
    {
        //dump($request->getModel()); die();
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var Payment $model */
        $model = $request->getModel();
       
        if (isset($model['state']) && 'approved' == $model['state']) {
            $request->markPayedout();
            dump('approved'); 
            return;
        }

        if (isset($model['state']) && 'created' == $model['state']) {
            if($request->isUnknown())
            {
                $request->markCanceled();
                $model['state']='canceled'; 
            } 
            else
            {
                $request->markNew();
                dump('created'); 
            }
            return;
        }

        

        if (false == isset($model['state'])) {
            $request->markCanceled();
            dump('canceled'); 
            return;
        }

        $request->markUnknown();
        dump('markUnknown'); 
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        if (false == $request instanceof GetStatusInterface) {
            return false;
        }

        /** @var Payment $model */
        $model = $request->getModel();
       if (false == $model instanceof PaymentARest) {
            return false;
        }

        
        return true;
    }
}
