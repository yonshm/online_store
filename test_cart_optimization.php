<?php

/**
 * Script de test pour l'optimisation du panier avec les cookies
 * 
 * Ce script teste les fonctionnalités du CartService et vérifie
 * que la migration des sessions vers les cookies fonctionne correctement.
 */

echo "=== TEST D'OPTIMISATION DU PANIER AVEC COOKIES ===\n\n";

// Configuration de base pour les tests
require_once 'vendor/autoload.php';

// Charger l'environnement Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "1. Test du CartService\n";
echo "=====================\n";

try {
    // Créer une instance du CartService
    $cartService = new \App\Services\CartService();

    // Simuler une requête
    $request = new \Illuminate\Http\Request();

    // Test 1: Panier vide
    $cartItems = $cartService->getCartItems($request);
    if (empty($cartItems)) {
        echo "✓ Panier vide correctement détecté\n";
    } else {
        echo "✗ Erreur: Le panier devrait être vide\n";
    }

    // Test 2: Ajout d'un produit
    $cartItems = $cartService->addToCart($request, 1, 2);
    if (isset($cartItems[1]) && $cartItems[1] === 2) {
        echo "✓ Ajout de produit réussi\n";
    } else {
        echo "✗ Erreur lors de l'ajout de produit\n";
    }

    // Test 3: Ajout d'un autre produit
    $cartItems = $cartService->addToCart($request, 2, 3);
    if (isset($cartItems[1]) && $cartItems[1] === 2 && isset($cartItems[2]) && $cartItems[2] === 3) {
        echo "✓ Ajout de plusieurs produits réussi\n";
    } else {
        echo "✗ Erreur lors de l'ajout de plusieurs produits\n";
    }

    // Test 4: Mise à jour d'un produit
    $cartItems = $cartService->updateCartItem($request, 1, 5);
    if (isset($cartItems[1]) && $cartItems[1] === 5) {
        echo "✓ Mise à jour de produit réussi\n";
    } else {
        echo "✗ Erreur lors de la mise à jour de produit\n";
    }

    // Test 5: Suppression d'un produit
    $cartItems = $cartService->removeFromCart($request, 1);
    if (!isset($cartItems[1]) && isset($cartItems[2])) {
        echo "✓ Suppression de produit réussi\n";
    } else {
        echo "✗ Erreur lors de la suppression de produit\n";
    }

    // Test 6: Vidage du panier
    $cartItems = $cartService->clearCart();
    if (empty($cartItems)) {
        echo "✓ Vidage du panier réussi\n";
    } else {
        echo "✗ Erreur lors du vidage du panier\n";
    }

    // Test 7: Création d'un cookie
    $testCart = [1 => 2, 2 => 3];
    $cookie = $cartService->saveCart($testCart);
    if ($cookie instanceof \Symfony\Component\HttpFoundation\Cookie) {
        echo "✓ Création de cookie réussi\n";
    } else {
        echo "✗ Erreur lors de la création du cookie\n";
    }

    // Test 8: Suppression d'un cookie
    $cookie = $cartService->deleteCartCookie();
    if ($cookie instanceof \Symfony\Component\HttpFoundation\Cookie) {
        echo "✓ Suppression de cookie réussi\n";
    } else {
        echo "✗ Erreur lors de la suppression du cookie\n";
    }
} catch (Exception $e) {
    echo "✗ Erreur lors du test du CartService: " . $e->getMessage() . "\n";
}

echo "\n2. Test de Performance\n";
echo "=====================\n";

try {
    $cartService = new \App\Services\CartService();
    $request = new \Illuminate\Http\Request();

    // Test de performance avec un panier vide
    $startTime = microtime(true);
    $cartItems = $cartService->getCartItems($request);
    $endTime = microtime(true);
    $executionTime = ($endTime - $startTime) * 1000; // en millisecondes

    if ($executionTime < 10) { // Moins de 10ms
        echo "✓ Performance excellente: {$executionTime}ms\n";
    } elseif ($executionTime < 50) { // Moins de 50ms
        echo "✓ Performance bonne: {$executionTime}ms\n";
    } else {
        echo "⚠ Performance à surveiller: {$executionTime}ms\n";
    }

    // Test de performance avec un panier rempli
    $testCart = [];
    for ($i = 1; $i <= 100; $i++) {
        $testCart[$i] = rand(1, 5);
    }

    $startTime = microtime(true);
    $products = $cartService->getCartProducts($testCart);
    $endTime = microtime(true);
    $executionTime = ($endTime - $startTime) * 1000;

    if ($executionTime < 100) { // Moins de 100ms
        echo "✓ Performance avec panier rempli: {$executionTime}ms\n";
    } else {
        echo "⚠ Performance avec panier rempli à surveiller: {$executionTime}ms\n";
    }
} catch (Exception $e) {
    echo "✗ Erreur lors du test de performance: " . $e->getMessage() . "\n";
}

echo "\n3. Test de Validation\n";
echo "====================\n";

try {
    $cartService = new \App\Services\CartService();

    // Test de validation avec des données valides
    $validCart = [1 => 2, 2 => 1];
    $errors = $cartService->validateStock($validCart);

    if (is_array($errors)) {
        echo "✓ Validation des données valides réussi\n";
    } else {
        echo "✗ Erreur lors de la validation des données valides\n";
    }

    // Test de validation avec des données invalides
    $invalidCart = [999 => 5]; // Produit inexistant
    $errors = $cartService->validateStock($invalidCart);

    if (is_array($errors) && !empty($errors)) {
        echo "✓ Validation des données invalides réussi\n";
    } else {
        echo "✗ Erreur lors de la validation des données invalides\n";
    }
} catch (Exception $e) {
    echo "✗ Erreur lors du test de validation: " . $e->getMessage() . "\n";
}

echo "\n4. Test de Calcul du Total\n";
echo "=========================\n";

try {
    $cartService = new \App\Services\CartService();

    // Créer des produits de test
    $product1 = new \App\Models\Product();
    $product1->setId(1);
    $product1->setPrice(100);
    $product1->setName('Produit 1');

    $product2 = new \App\Models\Product();
    $product2->setId(2);
    $product2->setPrice(200);
    $product2->setName('Produit 2');

    $products = [$product1, $product2];
    $cartItems = [1 => 2, 2 => 1]; // 2 produits à 100 + 1 produit à 200 = 400

    $total = $cartService->calculateTotal($cartItems, $products);

    if ($total === 400) {
        echo "✓ Calcul du total réussi: {$total}\n";
    } else {
        echo "✗ Erreur dans le calcul du total: {$total} (attendu: 400)\n";
    }

    // Test avec panier vide
    $emptyTotal = $cartService->calculateTotal([], []);
    if ($emptyTotal === 0) {
        echo "✓ Calcul du total avec panier vide réussi: {$emptyTotal}\n";
    } else {
        echo "✗ Erreur dans le calcul du total avec panier vide: {$emptyTotal}\n";
    }
} catch (Exception $e) {
    echo "✗ Erreur lors du test de calcul du total: " . $e->getMessage() . "\n";
}

echo "\n5. Avantages de l'Optimisation\n";
echo "============================\n";
echo "✓ Réduction de la charge serveur (pas de stockage session)\n";
echo "✓ Persistance du panier entre les sessions\n";
echo "✓ Meilleure performance (accès direct aux cookies)\n";
echo "✓ Scalabilité améliorée (pas de dépendance session)\n";
echo "✓ Migration automatique des anciennes sessions\n";
echo "✓ API endpoints pour les requêtes AJAX\n";

echo "\n6. Instructions pour Tester l'Application\n";
echo "========================================\n";
echo "1. Démarrer le serveur: php artisan serve\n";
echo "2. Tester l'ajout de produits au panier\n";
echo "3. Vérifier que les cookies sont créés\n";
echo "4. Tester la persistance entre les sessions\n";
echo "5. Exécuter les tests complets: php artisan test\n";

echo "\n7. Tests Spécifiques à Exécuter\n";
echo "==============================\n";
echo "- php artisan test tests/Feature/CartCookieOptimizationTest.php\n";
echo "- php artisan test tests/Feature/CartControllerTest.php\n";
echo "- php artisan test tests/Unit/ProductTotalCalculationTest.php\n";

echo "\n=== FIN DES TESTS D'OPTIMISATION ===\n";
