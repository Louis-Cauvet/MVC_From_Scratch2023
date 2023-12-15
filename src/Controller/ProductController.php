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
        return $this->twig->render('products/new.html.twig');
    }

    #[Route('/products/register', 'products_register', 'POST')]
    public function register(ProductRepository $pr, EntityManager $em): string
    {
        if (!isset($_POST['nom']) || !isset($_POST['prix'])) {
            $this->redirect('/products/new');
        }

        $nom = $_POST['nom'];
        $prix = $_POST['prix'];

        // on vérifie que le produit n'est pas connu dans la base de données
        $ProduitExistant = $pr->findOneBy(['name' => $nom]);
        if(!is_null($ProduitExistant)) {
            $prixProduitExistant = $ProduitExistant->getPrice();
            return $this->twig->render("products/new.html.twig", [
                'produitExiste' => $prixProduitExistant
            ]);
        }

        $nvProduit = new Product();
        $nvProduit->setName($nom);
        $nvProduit->setPrice($prix);


        $em->persist($nvProduit);
        $em->flush();

        return $this->twig->render("products/new.html.twig", [
             'product' => $nvProduit
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
