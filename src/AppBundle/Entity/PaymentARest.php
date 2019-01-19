<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Payum\Core\Model\ArrayObject;



/**
 * Payment
 *
 * @ORM\Table(name="paymentARest")
 * @ORM\Entity()
 */
class PaymentARest extends ArrayObject
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="idStorage", type="string", length=255, nullable=true)
     */
    private $idStorage;

    public function setIdStorage($idStorage)
    {
        $this->idStorage = $idStorage;

        return $this;
    }

    public function getIdStorage()
    {
        return $this->idStorage;
    }


    
}
