<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Entity\Product;
use App\Entity\SearchProduct;
use App\Form\CommentsType;
use App\Form\SearchProductType;
use App\Repository\HomeSliderRepository;
use App\Repository\ProductRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(ProductRepository $repoProduct, HomeSliderRepository $repoHomeSlider): Response
    {
        $products = $repoProduct->findAll();
        $homeSlider = $repoHomeSlider->findBy(['isDisplayed'=>true]);
        

        $productBestSeller = $repoProduct->findByIsBestSeller(1);
        $productSpecialOffer = $repoProduct->findByIsSpecialOffer(1);
        $productNewArrival = $repoProduct->findByIsNewArrival(1);
        $productFeatured = $repoProduct->findByIsFeatured(1);

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'products' => $products,
            'productBestSeller' => $productBestSeller, 
            'productSpecialOffer' => $productSpecialOffer, 
            'productNewArrival' => $productNewArrival, 
            'productFeatured' => $productFeatured,
            'homeSlider' => $homeSlider
        ]);
    }

    /**
     * @Route("/product/{slug}", name="product_details")
     */
    public function show(?Product $product, Request $request, EntityManagerInterface $manager):Response 
    {
        if(!$product){
            return $this->redirectToRoute('home');
        }

        //on crée le commentaire
        $comment = new Comments;

        //on génère le formulaire
        $form = $this->createForm(CommentsType::class, $comment);
        $form->handleRequest($request);

        // Traitement du formulaire
        if($form->isSubmitted() && $form->isValid()){
            $comment->setCreatedAt(new DateTime());
            $comment->setProduct($product);

            //on récupère le contenu du champ parentid
            $parentid = $form->get("parentid")->getData();

            //on va chercher le commentaire correspondant
            if($parentid != null){
                $parent = $manager->getRepository(Comments::class)->find($parentid);
            }
            

            // on définit le parent
            $comment->setParent($parent ?? null);
    
            $manager->persist($comment);
            $manager->flush();
            
            $this->addFlash('message','Votre commentaire a bien été envoyé');
            return $this->redirectToRoute('product_details', ['slug' => $product->getSlug()]);
        }

        return $this->render("home/single_product.html.twig",[
            'product' => $product,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/shop", name="shop")
     */
    public function shop(ProductRepository $repoProduct, Request $request): Response
    {
        $products = $repoProduct->findAll();

        $search = new SearchProduct();
        $form = $this->createForm(SearchProductType::class, $search);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $products = $repoProduct->findWithSearch($search);

        }
     
        return $this->render('home/shop.html.twig', [
            'products' => $products,
            'search' => $form->createView()
        ]);
    }
}
