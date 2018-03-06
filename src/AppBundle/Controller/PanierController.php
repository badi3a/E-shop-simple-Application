<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @Route("/panier")
 */

class PanierController extends Controller
{
    /**
     * @Route("/add/{id}",name="add_product_panier")
     */
    public function addAction(Product $product = null)
    {
        if($product) {
            $session=new Session();
            if($session->has('panier')){
                //the pannel already exist
                $panier=$session->get('panier');
                /*foreach ($panier as $item){
                    if($product->getId()== $item->getId()){
                        //update quanity
                    }else{
                        //add with quanitity=1
                    }
                }*/
                array_push($panier,$product);
                $session->set('panier',$panier);
            }else{
                $panier=[];
                array_push($panier,$product);
                $session->set('panier',$panier);
            }
            return $this->redirectToRoute('show_product_panier');
            /*return $this->render('AppBundle:Panier:show.html.twig', array(
                // ...
            ));*/
        } else {
            $msg="product not found";
            return $this->render('AppBundle:Panier:add.html.twig', array(
                'msg'=>$msg
            ));
        }
        return $this->render('AppBundle:Panier:add.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/remove/{id}",name="remove_product_panier")
     */
    public function removeAction(Product $product =null)
    {
        $session=new Session();
        if($session->has('panier')) {
            $panier = $session->get('panier');
            foreach ($panier as $index => $item) {
                if ($product->getId() == $item->getId()) {
                    unset($panier[$index]);
                    $session->set('panier',$panier);
                    return $this->redirectToRoute('show_product_panier');
                }
            }
        }else {
            $msg="product not found";
            return $this->render('AppBundle:Panier:show.html.twig', array(
                'msg'=>$msg
            ));
        }

    }

    /**
     * @Route("/show",name="show_product_panier")
     */
    public function showAction()
    {
        return $this->render('AppBundle:Panier:show.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/vider",name="empty_product_panier")
     */
    public function viderAction()
    {
        $session=new Session();
            //methode 1
            //$session->set('panier',[]);
        $session->remove('panier');
        return $this->redirectToRoute('show_product_panier');
    }

}
