<?php
namespace AppBundle\Controller;
use AppBundle\Entity\Partie;
use AppBundle\Form\PartieType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncode;
/**
 * Class JouerController
 * @package AppBundle\Controller
 * @Route("/jouer")
 */
class JouerController extends Controller
{
    /**
     * @Route("/nouvelle-partie")
     */
    public function nouvellePartieAction(Request $request)
    {
        $partie = new Partie();
        $form = $this->createForm(PartieType::class, $partie);
        $form->handleRequest($request); //synchronisation des données du formulaire avec l'objet $partie via le formType
        if ($form->isSubmitted() && $form->isValid())
        {
            //récupére la connexion à la BDD
            $em = $this->getDoctrine()->getManager();
            // initialisation des données de la partie
            //récupération de toutes les bornes
            $bornes = $em->getRepository("AppBundle:Borne")->findAll();
            $tborne=array(); //tableau qui sera sauvegardé dans la BDD
            $ordre = 1; //ordre des bornes
            foreach ($bornes as $borne)
            {
                $tborne[$ordre] = array('id_borne' => $borne->getId(),
                    'position' => 'neutre');
                $ordre ++;
            }
            //sauvegarde la liste des bornes dans ma partie
            $partie->setListeDesBornes($tborne);
            $cartes = $em->getRepository('AppBundle:Carte')->findAll();
            $tcarte = array();
            foreach ($cartes as $carte)
            {
                $tcarte[] = $carte->getId(); //sauvegarde les id des cartes dans un tableau
            }
            shuffle($tcarte); //mélange du tableau
            //distribution de la main de J1
            $mainJ1=array();
            for($i = 0; $i<6; $i++)
            {
                $mainJ1[] = $tcarte[$i];
            }
            $partie->setMainj1($mainJ1);
            //distributoon de la main de J2
            $mainJ2=array();
            for($i = 6; $i<12; $i++)
            {
                $mainJ2[] = $tcarte[$i];
            }
            $partie->setMainj2($mainJ2);
            $pioche=array();
            for($i = 12; $i < count($tcarte); $i++)
            {
                $pioche[] = $tcarte[$i];
            }
            $partie->setPioche($pioche);
            $partie->setTourJoueur($partie->getJoueur1());
            $terrain = array(
                'col1' => array(0,0,0),
                'col2' => array(0,0,0),
                'col3' => array(0,0,0),
                'col4' => array(0,0,0),
                'col5' => array(0,0,0),
                'col6' => array(0,0,0),
                'col7' => array(0,0,0),
                'col8' => array(0,0,0),
                'col9' => array(0,0,0)
            );
            $partie->setTerrainj1($terrain);
            $partie->setTerrainj2($terrain);
            $em->persist($partie);
            $em->flush();
            // redirection vers la distribution des cartes
            return $this->redirectToRoute("affiche_plateau", array('partie' => $partie->getId()) );
        }
        return $this->render(':JouerController:nouvelle_partie.html.twig', array(
            'form' => $form->createView()
        ));
    }
    /**
     * @Route("/afficher/{partie}", name="affiche_plateau")
     */
    public function afficherPlateauAction(Partie $partie)
    {
        // Afficher le plateau
        //récupérer cartes et bornes
        $em = $this->getDoctrine()->getManager();
        $cartes = $em->getRepository('AppBundle:Carte')->findAll();
        $bornes = $em->getRepository('AppBundle:Borne')->findAll();
        //construction d'un tableau d'ibjet carte dont l'index est id
        $tcartes = array();
        foreach ($cartes as $carte)
        {
            $tcartes[$carte->getId()] = $carte;
        }
        $tbornes = array();
        foreach ($bornes as $borne)
        {
            $tbornes[$borne->getId()] = $borne;
        }
        $montour = false;
        if ($this->getUser()->getId() == $partie->getTourJoueur()->getId())
        {
            $montour = true;
            if ($partie->getTourJoueur()->getId() == $partie->getJoueur1()->getId())
            {
                //c'est le joueur 1
                $nomadversaire = 'j2';
                $nomencours = 'j1';
                $adversaire = $partie->getJoueur2();
                $mainencours = $partie->getMainj1();
                $terrainencours = $partie->getTerrainj1();
                $terrainadversaire = $partie->getTerrainj2();
            } else
            {
                //c'est le joueur 2
                $nomadversaire = 'j1';
                $nomencours = 'j2';
                $adversaire = $partie->getJoueur1();
                $mainencours = $partie->getMainj2();
                $terrainencours = $partie->getTerrainj2();
                $terrainadversaire = $partie->getTerrainj1();
            }
        } else
        {
            $montour = false; //ce n'est pas mon tour de jeu
            if ($this->getUser()->getId() == $partie->getJoueur1()->getId())
            {
                //c'est le joueur 1
                $nomadversaire = 'j2';
                $nomencours = 'j1';
                $adversaire = $partie->getJoueur2();
                $mainencours = $partie->getMainj1();
                $terrainencours = $partie->getTerrainj1();
                $terrainadversaire = $partie->getTerrainj2();
            } else
            {
                //c'est le joueur 2
                $nomadversaire = 'j1';
                $nomencours = 'j2';
                $adversaire = $partie->getJoueur1();
                $mainencours = $partie->getMainj2();
                $terrainencours = $partie->getTerrainj2();
                $terrainadversaire = $partie->getTerrainj1();
            }
        }
        return $this->render(':JouerController:afficher_plateau.html.twig', array(
            'partie' => $partie,
            'tcartes' => $tcartes,
            'tbornes' => $tbornes,
            'mainencours' => $mainencours,
            'terrainencours' => $terrainencours,
            'terrainadversaire' => $terrainadversaire,
            'adversaire' => $adversaire,
            'user'=> $this->getUser(),
            'montour' => $montour,
            'nomadversaire' => $nomadversaire,
            'nomencours' => $nomencours
        ));
    }
    /**
     * @Route("/ajax/jouercarte", name="jouer_carte")
     */
    public function sauvegarderDeplacementAction(Request $request)
    {
        $colonne = $request->request->get('colonne');
        $idcarte = $request->request->get('carte');
        $idpartie = $request->request->get('partie');
        $em = $this->getDoctrine()->getManager();
        $partie = $em->getRepository('AppBundle:Partie')->find($idpartie);
        if ($this->getUser()->getId() == $partie->getJoueur1()->getId()) {
            $terrainJ1 = $partie->getTerrainj1();
            $i = 0;
            $carteplace = false;
            //sauvegarde l'id de la carte dans le terrain du joueur 1.
            while ($carteplace == false) {
                if ($terrainJ1['col' . $colonne][$i] == 0) {
                    //alors la zone est libre
                    $terrainJ1['col' . $colonne][$i] = $idcarte;
                    $carteplace = true;
                }
                $i++;
            }
            $mainj1 = $partie->getMainj1();
            $index = array_search($idcarte, $mainj1);
            unset($mainj1[$index]);
            $mainj1 = array_values($mainj1);
            //Supprimer la carte de la main du joueur.
            $partie->setTerrainj1($terrainJ1);
            $partie->setMainj1($mainj1);
        } else
        {
            $terrainJ2 = $partie->getTerrainj2();
            $i = 0;
            $carteplace = false;
            //sauvegarde l'id de la carte dans le terrain du joueur 1.
            while ($carteplace == false) {
                if ($terrainJ2['col' . $colonne][$i] == 0) {
                    //alors la zone est libre
                    $terrainJ2['col' . $colonne][$i] = $idcarte;
                    $carteplace = true;
                }
                $i++;
            }
            $mainj2 = $partie->getMainj2();
            $index = array_search($idcarte, $mainj2);
            unset($mainj2[$index]);
            $mainj2 = array_values($mainj2);
            //Supprimer la carte de la main du joueur.
            $partie->setTerrainj2($terrainJ2);
            $partie->setMainj2($mainj2);
        }
        $em->persist($partie);
        $em->flush();
        return new Response('ok', 200);
    }
    /**
     * @Route("/piocher/{partie}", name="jouer_piocher")
     */
    public function piocherAction(Partie $partie)
    {
        if (count($partie->getMainj1()) < 6)
        {

            $pioche = $partie->getPioche();
            $carte = $pioche[0];
            unset($pioche[0]);
            $pioche = array_values($pioche);
            $em = $this->getDoctrine()->getManager();
            $partie->setPioche($pioche);
            if ($this->getUser()->getId() == $partie->getJoueur1()->getId()) {
                $mainJ1 = $partie->getMainj1();
                $mainJ1[] = $carte;
                $partie->setMainj1($mainJ1);
                $partie->setTourJoueur($partie->getJoueur2());
            } else
            {
                $mainJ2 = $partie->getMainj2();
                $mainJ2[] = $carte;
                $partie->setMainj2($mainJ2);
                $partie->setTourJoueur($partie->getJoueur1());
            }
            $em->persist($partie);
            $em->flush();
            return $this->redirectToRoute('affiche_plateau', array(
                'partie' => $partie->getId()
            ));

        }
        else {

            return $this->redirectToRoute('affiche_plateau', array(
                'partie' => $partie->getId() ));
        }
    }
    /**
     * @Route("/revendiquerBorne")
     */
    public function revendiquerBorneAction()
    {
        return $this->render(':JouerController:revendiquer_borne.html.twig', array(
            // ...
        ));
    }
}