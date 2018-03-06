<?php

namespace AppBundle\Controller;

use AppBundle\AppBundle;
use AppBundle\Entity\Category;
use AppBundle\Entity\Product;
use function PHPSTORM_META\type;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\DomCrawler\Tests\Field\TextareaFormFieldTest;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Tests\Extension\Core\Type\IntegerTypeTest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class DefaultController extends Controller
{

    /**
     * @Route("/contact")
     */
    public function indexAction()
    {
        return $this->render('AppBundle:Default:contact.html.twig');
    }
    /**
     * @Route("/home",name="homepage")
     */
    public function bootAction(){
        return $this->render('AppBundle:Default:bootsrap.html.twig');
    }
//    /**
//     * @Route("/{name}")
//     */
//    public function nameAction($name){
//        return $this->render('AppBundle:Default:hellouser.html.twig',['name'=>$name ]);
//    }

        //Ajout statique des produits//

     /**
      * @Route("/new",name="new_product_category")
      */
     public function newAction(){
         //create category
         $category= new Category();
         $category->setName('SmartPhone');
         //create product
         $product =new Product();
         $product->setName('Nokia');
         $product->setDescription('test');
         $product->setPrice('350');
         $product->setCategory($category);
         $product->setQuantity(10);
         $category->addProduct($product);
         //now we need to relate these ojects to our data base
         //here doctrine already heriteted from the contoroller
         $em=$this->getDoctrine()->getManager(); //get entity Manager
         //prepare our package to be added to data base
         $em->persist($product);
         $em->persist($category);
         //now we will add this package to the data base
         $em->flush();
         //test our page if it's working
         $response= new Response();
         $response->setContent("<h2>created!</h2>");
         return $response;
     }
    /**
     * @Route("/homePro",name="home_product_category")
     */
    public function homeProAction(Request $request){
        $em=$this->getDoctrine()->getManager(); //get entity Manager
        $produits=$em->getRepository('AppBundle:Product')->findByQte(0);
        $produits= $this->get('knp_paginator')->paginate($produits,$request->query->get('page',1),6);
        $categories=$em->getRepository('AppBundle:Category')->findAll();
        return $this->render('@App/Default/homePro.html.twig',['produits'=>$produits,'categories'=>$categories]);

    }
    /**
     * @Route("/product/{id}",name="aff_product_category")
     */
    public function AfficheProAction(Product $product= null){
        if($product){
            $em=$this->getDoctrine()->getManager(); //get entity Manager
            $categories=$em->getRepository('AppBundle:Category')->findAll();
            return $this->render('@App/Default/affPro.html.twig',['product'=>$product,'categories'=>$categories]);
        }
        else {
            $response= new Response();
            $response->setContent("<h2>error not found</h2>");
            return $response;
        }}
        /**
         * @Route("/category/{id}",name="aff_by_category")
         */
        public function AfficheCatAction(Request $request,Category $category= null){
            if($category){
                $products=$category->getProducts();
                $em=$this->getDoctrine()->getManager(); //get entity Manager
                $categories=$em->getRepository('AppBundle:Category')->findAll();
                $products= $this->get('knp_paginator')->paginate($products,$request->query->get('page',1),6);
                return $this->render('@App/Default/affCat.html.twig',['products'=>$products,'categories'=>$categories,'categoryAct'=>$category]);
            }
            else {
                $response= new Response();
                $response->setContent("<h2>error not found</h2>");
                return $response;
            }

    }



    //Add product with a form
    /**
     * @Route("/form_product",name="form_product")
     */
    public function formAction(Request $request){
        $em =$this->getDoctrine()->getManager();
        $products= new Product();
        $form=$this->createFormBuilder($products)
            ->add('name',TextType::class)
            ->add('price',TextType::class)
            ->add('description',TextareaType::class)
            ->add('quantity',IntegerType::class)
            ->add('category',EntityType::class,[
                'class'=>'AppBundle\Entity\Category',
                'choice_label'=>'name'
            ])->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted()&& $form->isValid()){
            $em->persist($products);
            $em->flush();
//            $session= new Session();
//            $session->getFlashBag()->add('prod_created','Product created!!');

            return $this->redirectToRoute('aff_product_category',['id'=>$products->getId()]);
        }
        return $this->render('AppBundle:Default:formPro.html.twig',['form'=>$form->createView()]);
}}

