<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Routing\Attribute\Route;
use Doctrine\ORM\EntityManager;

class ProductController extends AbstractController
{
    #[Route('/products/new', 'products_new')]
    public function new(EntityManager $em): string
    {
        $product = new Product();
        $product
            ->setName(name: "sIgTKZNDZrr")
            ->setPrice(76.24);

        $em->persist(entity: $product);
        $em->flush();

        return $this->twig->render("products/new.html.twig", [
            'product' => $product
        ]);
    }

    #[Route('/products/list', 'products_list')]
    public function list(ProductRepository $productRepository): string
    {
        return $this->twig->render('products/list.html.twig', [
            'products' => $productRepository->findAll()
        ]);
    }

    // #[Route('/products/{id}', 'products_item')]
    // public function item(ProductRepository $productRepository, int $id): string
    // {

    //     $product = find()
    //     // Définition route : products/{id}   --> identifier à l'aide d'une expression régulière
    //     // modifier le "getRoute()" dans le router pour y ajouter la gestion d'expression régulière (en se limitant aux int comme paramètre "id") --> "/\{(\d+)\}/"
    //     // gérer les erreurs
    //     return $this->twig->render('item...');
    // }
}
