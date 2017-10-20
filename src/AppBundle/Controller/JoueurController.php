<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class JouerController
 * @package AppBundle\Controller
 * @Route("/joueur")
 */
class JoueurController extends Controller
{
    /**
     * @Route("/mes_parties")
     */
    public function mes_partiesAction()
    {
        $id = $this->getUser()->getId();
        $partiej1 = $this->getDoctrine()->getRepository('AppBundle:Partie')->findBy(array('joueur1'=>$id));
        $partiej2 = $this->getDoctrine()->getRepository('AppBundle:Partie')->findBy(array('joueur2'=>$id));


        return $this->render('AppBundle:Joueur:mes.parties.html.twig', array(
            'partie1' => $partiej1,
            'partie2' => $partiej2,
            'user' => $this->$id,

        ));
    }

}
