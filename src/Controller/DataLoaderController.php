<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Entity\Product;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DataLoaderController extends AbstractController
{
    /**
     * @Route("/data", name="data_loader")
     */
    public function index(EntityManagerInterface $manager, UserRepository $repoUser): Response
    {

        $file_products = dirname(dirname(__DIR__))."\products.json";
        $file_categories = dirname(dirname(__DIR__))."\categories.json";
        $data_products = json_decode(file_get_contents($file_products))[0]->rows;
        $data_categories = json_decode(file_get_contents($file_categories))[0]->rows;

        $categories = [];

        foreach ($data_categories as $data_category) {
            $category = new Categories();
            $category->setName($data_category[1])
                    ->setImage($data_category[3]);
            // $manager->persist($category);
            $categories[] = $category;
        }

        foreach ($data_products as $data_Product) {
            $product = new Product();
            $product->setName($data_Product[1])
                    ->setDescription($data_Product[2])
                    ->setPrice($data_Product[4])
                    ->setIsBestSeller($data_Product[5])
                    ->setIsNewArrival($data_Product[6])
                    ->setIsFeatured($data_Product[7])
                    ->setIsSpecialOffer($data_Product[8])
                    ->setImage($data_Product[9])
                    ->setQuantity($data_Product[10])
                    ->setTags($data_Product[12])
                    ->setSlug($data_Product[13])
                    ->setCreatedAt(new \DateTime());
            // $manager->persist($product);
            $products[] = $product;
        }

        // $user = $repoUser->find(2);
        // $user->setRoles(['ROLE_ADMIN']);
        // $manager->flush();

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/DataLoaderController.php',
        ]);
    }
}
