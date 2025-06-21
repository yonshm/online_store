# Documentation des Tests Unitaires et de Fonctionnalités

## Vue d'ensemble

Cette documentation décrit les tests unitaires et de fonctionnalités créés pour l'application de boutique en ligne Laravel. Les tests couvrent les aspects critiques de l'application : calcul du total, actions des contrôleurs et fonctionnalités des modèles.

## Tests Créés

### 1. Tests Unitaires pour le Calcul du Total

**Fichier :** `tests/Unit/ProductTotalCalculationTest.php`

#### Objectif

Tester la méthode `sumPricesByQuantities` du modèle Product qui calcule le total des produits dans le panier.

#### Tests Inclus

1. **`test_calculate_total_single_product()`**

    - Teste le calcul avec un seul produit
    - Vérifie que le total = prix × quantité

2. **`test_calculate_total_multiple_products()`**

    - Teste le calcul avec plusieurs produits
    - Vérifie la somme correcte de tous les produits

3. **`test_calculate_total_with_zero_quantity()`**

    - Teste le comportement avec quantité zéro
    - Vérifie que le total est 0

4. **`test_calculate_total_with_decimal_prices()`**

    - Teste le calcul avec des prix décimaux
    - Vérifie la précision des calculs

5. **`test_calculate_total_with_missing_session_products()`**

    - Teste le comportement avec des produits manquants dans la session
    - Vérifie que seuls les produits présents sont calculés

6. **`test_calculate_total_with_empty_products()`**

    - Teste le comportement avec une liste vide de produits
    - Vérifie que le total est 0

7. **`test_calculate_total_with_negative_quantities()`**
    - Teste le comportement avec des quantités négatives
    - Vérifie que le calcul fonctionne correctement

#### Exemple d'Utilisation

```php
$products = collect([$product1, $product2]);
$productsInSession = [1 => 2, 2 => 1];
$total = Product::sumPricesByQuantities($products, $productsInSession);
// Résultat: (prix1 × 2) + (prix2 × 1)
```

### 2. Tests de Fonctionnalités pour le CartController

**Fichier :** `tests/Feature/CartControllerTest.php`

#### Objectif

Tester toutes les actions du CartController, y compris l'authentification, la gestion des sessions et les interactions avec la base de données.

#### Tests Inclus

1. **`test_cart_index_with_empty_cart()`**

    - Teste l'affichage du panier vide
    - Vérifie la vue et les données transmises

2. **`test_cart_index_with_products()`**

    - Teste l'affichage du panier avec des produits
    - Vérifie le calcul du total et l'affichage des produits

3. **`test_add_product_to_cart()`**

    - Teste l'ajout d'un produit au panier
    - Vérifie la session et les quantités

4. **`test_add_product_with_quantity_exceeding_stock()`**

    - Teste l'ajout avec quantité supérieure au stock
    - Vérifie les messages d'erreur

5. **`test_add_nonexistent_product_to_cart()`**

    - Teste l'ajout d'un produit inexistant
    - Vérifie la gestion des erreurs 404

6. **`test_delete_cart()`**

    - Teste la suppression du panier
    - Vérifie que la session est vidée

7. **`test_purchase_with_empty_cart()`**

    - Teste l'achat avec panier vide
    - Vérifie la redirection

8. **`test_purchase_with_products_in_cart()`**

    - Teste l'achat avec des produits
    - Vérifie la mise à jour des stocks et du solde utilisateur

9. **`test_add_product_with_invalid_quantity()`**

    - Teste l'ajout avec quantité invalide
    - Vérifie la validation

10. **`test_cart_access_without_authentication()`**
    - Teste l'accès sans authentification
    - Vérifie la redirection vers login

#### Exemple d'Utilisation

```php
$response = $this->actingAs($user)
                ->post("/cart/add/{$product->id}", ['quantity' => 2]);
$response->assertRedirect('/cart');
```

### 3. Tests Unitaires pour le Modèle Product

**Fichier :** `tests/Unit/ProductModelTest.php`

#### Objectif

Tester toutes les fonctionnalités du modèle Product, y compris les relations, la validation et les calculs de prix avec remises.

#### Tests Inclus

1. **`test_can_create_product()`**

    - Teste la création d'un produit
    - Vérifie les attributs et la persistance en base

2. **`test_product_getters_and_setters()`**

    - Teste tous les getters et setters
    - Vérifie la cohérence des données

3. **`test_product_category_relationship()`**

    - Teste la relation avec la catégorie
    - Vérifie l'accès aux données liées

4. **`test_product_supplier_relationship()`**

    - Teste la relation avec le fournisseur
    - Vérifie l'accès aux données liées

5. **`test_product_validation()`**

    - Teste la validation avec des données valides
    - Vérifie que la validation passe

6. **`test_product_validation_with_invalid_data()`**

    - Teste la validation avec des données invalides
    - Vérifie que les erreurs sont levées

7. **`test_get_discounted_price()`**

    - Teste le calcul du prix avec remise globale
    - Vérifie l'application correcte des remises

8. **`test_get_discounted_price_with_category_discount()`**

    - Teste le calcul avec remise de catégorie
    - Vérifie l'application des remises spécifiques

9. **`test_get_discounted_price_with_product_discount()`**

    - Teste le calcul avec remise de produit
    - Vérifie l'application des remises individuelles

10. **`test_get_discounted_price_with_multiple_discounts()`**

    - Teste le calcul avec plusieurs remises
    - Vérifie l'accumulation des remises

11. **`test_get_discounted_price_without_discount()`**

    - Teste le calcul sans remise
    - Vérifie que le prix original est retourné

12. **`test_get_discounted_price_with_maximum_discount()`**

    - Teste la limite maximale de remise (90%)
    - Vérifie que les remises sont plafonnées

13. **`test_update_stock()`**

    - Teste la mise à jour du stock
    - Vérifie la persistance des changements

14. **`test_product_items_relationship()`**
    - Teste la relation avec les items
    - Vérifie l'accès aux commandes

#### Exemple d'Utilisation

```php
$product = Product::create([...]);
$discountedPrice = $product->getDiscountedPrice();
// Retourne le prix avec remises appliquées
```

## Exécution des Tests

### Prérequis

1. Base de données configurée
2. Migrations exécutées
3. Seeders exécutés (si nécessaire)

### Commandes d'Exécution

#### Tous les tests

```bash
php artisan test
```

#### Tests spécifiques

```bash
# Tests unitaires pour le calcul du total
php artisan test tests/Unit/ProductTotalCalculationTest.php

# Tests de fonctionnalités pour le CartController
php artisan test tests/Feature/CartControllerTest.php

# Tests unitaires pour le modèle Product
php artisan test tests/Unit/ProductModelTest.php
```

#### Tests avec couverture

```bash
php artisan test --coverage
```

### Script de Test Rapide

Un script PHP est disponible pour tester rapidement les fonctionnalités de base :

```bash
php run_tests.php
```

## Structure des Tests

### Tests Unitaires (`tests/Unit/`)

-   **ProductTotalCalculationTest.php** : Tests du calcul du total
-   **ProductModelTest.php** : Tests du modèle Product

### Tests de Fonctionnalités (`tests/Feature/`)

-   **CartControllerTest.php** : Tests du CartController

### Configuration

-   Utilisation de `RefreshDatabase` pour isoler les tests
-   Utilisation de `WithFaker` pour générer des données de test
-   Configuration des factories pour les modèles

## Bonnes Pratiques Appliquées

1. **Isolation des Tests**

    - Chaque test est indépendant
    - Utilisation de `RefreshDatabase` pour nettoyer la base

2. **Nommage Clair**

    - Noms de méthodes descriptifs
    - Documentation des tests

3. **Couverture Complète**

    - Tests des cas normaux et d'erreur
    - Tests des limites et cas extrêmes

4. **Assertions Appropriées**

    - Vérification des valeurs attendues
    - Vérification des états de la base de données
    - Vérification des réponses HTTP

5. **Données de Test Réalistes**
    - Utilisation de données cohérentes
    - Simulation d'utilisateurs et de produits

## Maintenance des Tests

### Ajout de Nouveaux Tests

1. Créer le fichier de test dans le bon répertoire
2. Suivre la convention de nommage
3. Ajouter la documentation appropriée
4. Exécuter les tests pour vérifier

### Mise à Jour des Tests

1. Identifier les changements dans le code
2. Mettre à jour les tests correspondants
3. Vérifier que tous les tests passent
4. Mettre à jour la documentation si nécessaire

### Debugging des Tests

1. Utiliser `dd()` pour inspecter les données
2. Vérifier la configuration de la base de données
3. S'assurer que les migrations sont à jour
4. Vérifier les dépendances des modèles

## Conclusion

Ces tests fournissent une couverture complète des fonctionnalités critiques de l'application :

-   Calculs financiers (totaux, remises)
-   Gestion du panier d'achat
-   Fonctionnalités des modèles
-   Validation des données
-   Relations entre entités

L'exécution régulière de ces tests garantit la stabilité et la fiabilité de l'application.
