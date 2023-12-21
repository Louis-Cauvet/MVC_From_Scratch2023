# Rendu Louis Cauvet : Améliorations sur le MVC

### Amélioration principale : Gestion des urls possèdant des paramètres
**Objectif** --> Gérer des urls de type */products/{id}* afin de pouvoir afficher les informations d'un produit en particulier, selon son identifiant passé en paramètres d'url.

#### Etape 1 : Création d'une page permettant d'ajouter un produit dans la bdd
1) Création du template de la page (*templates/products/new.html.twig*), qui contient un formulaire permettant de rentrer les données du nouveau produit. 
2) Création du contrôleur `register()` appelé lors de la soumission du formulaire d'ajout (dans le groupe de contrôleurs *src/Controller/ProductController.php*). 
> **Remarque** : J'effectue une double vérification dans ce contrôleur avant de rentrer les données dans la bdd :
-  Je vérifie d'abord que tous les champs du formulaire sont bien renseignés :
```
if (!isset($_POST['nom']) || !isset($_POST['prix'])) {
    $this->redirect('/products/new');
}
```
- Puis je vérifie que le produit n'existe pas déjà dans la base (on part du principe qu'il ne peut pas y avoir 2 produits avec des noms identiques), auquel cas j'affiche un message d'erreur dans le template en indiquant son prix :
```
$ProduitExistant = $pr->findOneBy(['name' => $nom]);
if(!is_null($ProduitExistant)) {
    $prixProduitExistant = $ProduitExistant->getPrice();
    return $this->twig->render("products/new.html.twig", [
        'produitExiste' => $prixProduitExistant
    ]);
}
```

#### Etape 2 : Création d'une page permettant de consulter la catalogue des produits disponibles
1) Création du template de la page (*templates/products/list.html.twig*), qui liste les produits existants dans la base à l'aide d'une boucle `for`. Dans ce template, j'attribue à chaque élément un lien qui pointe vers l'url "*/product/{id}*" (où {id} est l'identifiant de l'élément), pour pouvoir accéder à sa future page de consultation. 
> **Remarque** : J'ai géré aussi le cas où il n'y a aucun produit enregistré dans la base en affichant un message d'erreur
2) Création du contrôleur `list()`, qui permet de récupérer tous les produits stockés en base à l'aide du `ProductRepository` (dans le groupe de contrôleurs *src/Controller/ProductController.php*).


#### Etape 3 : Mise en place du routage prenant en compte le paramètres passé dans l'url


#### Etape 12 : Ajout de style pour les pages 
