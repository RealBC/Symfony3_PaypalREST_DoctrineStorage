<?php

namespace AppBundle\Payum\PaypalRestDoctrine\Action;

use PayPal\Api\Payment as PaypalPayment;

use AppBundle\Entity\PaymentARest;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\Sync;

class SyncAction implements ActionInterface, GatewayAwareInterface
{

    use GatewayAwareTrait;

    /**
     * {@inheritDoc}
     */
    public function execute($request)
    {
        dump($request); die();
        /** @var $request Sync */
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var PaypalPayment $model */
        $model = $request->getModel();

        $payment = PaypalPayment::get($model->id);
        dump('SYNC'); 
        $model->fromArray($payment->toArray());
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Sync &&
            $request->getModel() instanceof PaymentARest || $request->getModel() instanceof PaypalPayment
        ;
    }
}
