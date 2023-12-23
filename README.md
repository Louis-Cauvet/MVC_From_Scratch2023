# Rendu Louis Cauvet : Améliorations sur le MVC

### Amélioration principale : Gestion des urls possèdant des paramètres
**Objectif** --> Gérer des urls de format */products/{id}* afin de pouvoir afficher les informations d'un produit en particulier, selon son identifiant passé en paramètres d'url.

#### Etape 1 : Création d'une page permettant d'ajouter un produit dans la bdd
1) Création du template de la page (*templates/products/new.html.twig*), qui contient un formulaire permettant de rentrer les données du nouveau produit. \
![Capture page nouveau produit](https://github.com/Louis-Cauvet/MVC_From_Scratch2023/blob/main/Captures/Capture_1.png)
   
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
1) Création du contrôleur `list()`, qui permet de récupérer tous les produits stockés en base à l'aide du `ProductRepository` (dans le groupe de contrôleurs *src/Controller/ProductController.php*).
   
2) Création du template de la page (*templates/products/list.html.twig*), qui liste les produits existants dans la base à l'aide d'une boucle `for`. Dans ce template, j'attribue à chaque élément un lien qui pointe vers l'url "*/product/{id}*" (où {id} est l'identifiant de l'élément), pour pouvoir accéder à sa future page de consultation.
![Capture page nouveau produit](https://github.com/Louis-Cauvet/MVC_From_Scratch2023/blob/main/Captures/Capture_2.png)
> **Remarque** : J'ai géré aussi le cas où il n'y a aucun produit enregistré dans la base en affichant un message d'erreur



#### Etape 3 : Mise en place du routage prenant en compte le paramètres passé dans l'url
1) Création du contrôleur `item()` lié aux urls de format */products/{id}*, qui permet de rechercher dans la bdd un produit selon son identifiant passé en paramètres (dans le groupe de contrôleurs *src/Controller/ProductController.php*).
> **Remarque** : J'ai géré le cas où l'identifiant de l'url ne correspond à aucun produit, afin de rediriger l'utilisateur vers la page de catalogue.

2) Modification de la fonction `getRoute()` de *src/Routing/Router.php*, afin d'identifier si l'url demandée est au format */products/{id}*. Pour cela, j'effectue une double vérification à l'aide de 2 expressions régulières :
```
$regExpr = "/\{(\w+)\}/";
$regExpr2 = "#/products/(\d+)#";

foreach ($this->routes as $savedRoute) {
    // on vérifie si l'url de la requête est au format "/products/{id}", avec {id} qui vaut un nombre entier
    if(preg_match($regExpr, $savedRoute->getUri()) && preg_match($regExpr2, $uri)) {
        return $savedRoute;
    }
```
> **Remarque** : Cette partie pourrait sûrement être encore optimisée, afin d'effectuer la vérification en dehors de la fonction  `getRoute()`.

3) Modification de la fonction `execute()` de *src/Routing/Router.php*, afin d'isoler le cas où le contrôleur demandé serait `item()`. Si c'est le cas, on divise l'url demandée en plusieurs morceaux afin de récupérer le dernier, correspondant à l'identifiant du produit, avant de le passer en paramètres du contrôleur :
```
// si le contrôleur est "item()", on récupère l'id contenu dans l'url pour le passer dans ses paramètres
if($method == "item") {
    $tabUri = explode('/', $uri);
    $idProductUri = intval(end($tabUri));
}

$controllerParams = $this->getMethodParams($controllerClass . '::' . $method);
if(isset($idProductUri)) {
    return $controllerInstance->$method($idProductUri, ...$controllerParams);
} else {
    return $controllerInstance->$method(...$controllerParams);
}
```
> **Remarque** : Cette modification nécéssite également de changer la méthode `getMethodParams()`du routeur, afin de ne pas ajouter l'identifiant du produit dans le tableau de paramètres puisqu'il est géré à part dans `execute()`:
```
if($method === "App\Controller\ProductController::item" && $paramTypeFQCN === "int") {
    continue;
} else {
    $params[] = $this->container->get($paramTypeFQCN);
}
```

4) Création du template de la page (*templates/products/detail.html.twig*), qui affiche les informations en détail du produit désigné.
![Capture page nouveau produit](https://github.com/Louis-Cauvet/MVC_From_Scratch2023/blob/main/Captures/Capture_3.png)

#### Etape 4 : Ajout de style pour les pages 
1) Création d'un répertoire "public/styles" contenant un fichier "style.css" qui est lié au template parent "base.html.twig" afin d'appliquer du style sur toutes les pages qui en découlent.
> **Remarque** : J'ai placé le répertoire "styles" dans "public" pour qu'il soit facilement accessible avec un chemin relatif, mais je ne sais pas si c'est le meilleur endroit pour le placer...
