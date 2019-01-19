<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Payum\Core\Model\Token;

/**
 * PaymentToken
 *
 * @ORM\Table(name="payment_token")
 * @ORM\Entity()
 */
class PaymentToken extends Token
{
   
}

