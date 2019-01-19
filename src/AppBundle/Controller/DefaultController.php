<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use EXS\PhpUserAgentBundle\Services\PhpUserAgentService;

class DefaultController extends Controller
{
    /**
     * 
     * @Route("/", name="homepage")
     *
     */
    public function indexAction(Request $request)
    {
       
            return $this->render('default/index.html.twig');

    }
    
    
}
