<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends Controller
{

    /**
     * @Route("/page1", name="page_un")
     */

    public function premierePageAction()
    {
        return $this->render('Test/page1.html.twig');
    }



    /**
     * @Route("/page2", name="page_deux")
     */

    public function deuxiemePageAction()
    {
        return $this->render('Test/page2.html.twig');
    }





}
