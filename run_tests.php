<?php

/**
 * Script pour exécuter les tests unitaires et de fonctionnalités
 * 
 * Ce script permet d'exécuter les tests créés pour :
 * - Calcul du total des produits
 * - Actions du CartController
 * - Modèle Product
 */

echo "=== TESTS UNITAIRES ET DE FONCTIONNALITÉS ===\n\n";

// Configuration de base pour les tests
require_once 'vendor/autoload.php';

// Charger l'environnement Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "1. Test de calcul du total des produits\n";
echo "=====================================\n";

// Test de la méthode sumPricesByQuantities
try {
    // Créer des produits de test
    $product1 = new \App\Models\Product();
    $product1->setId(1);
    $product1->setPrice(100);
    $product1->setName('Produit 1');

    $product2 = new \App\Models\Product();
    $product2->setId(2);
    $product2->setPrice(200);
    $product2->setName('Produit 2');

    $products = collect([$product1, $product2]);
    $productsInSession = [
        1 => 2, // 2 quantités pour le produit 1
        2 => 1  // 1 quantité pour le produit 2
    ];

    $total = \App\Models\Product::sumPricesByQuantities($products, $productsInSession);
    $expectedTotal = (100 * 2) + (200 * 1); // 400

    if ($total === $expectedTotal) {
        echo "✓ Calcul du total réussi: {$total} (attendu: {$expectedTotal})\n";
    } else {
        echo "✗ Erreur dans le calcul du total: {$total} (attendu: {$expectedTotal})\n";
    }

    // Test avec un seul produit
    $singleProduct = collect([$product1]);
    $singleSession = [1 => 3];
    $singleTotal = \App\Models\Product::sumPricesByQuantities($singleProduct, $singleSession);
    $expectedSingleTotal = 100 * 3; // 300

    if ($singleTotal === $expectedSingleTotal) {
        echo "✓ Calcul du total avec un seul produit réussi: {$singleTotal}\n";
    } else {
        echo "✗ Erreur dans le calcul du total avec un seul produit: {$singleTotal}\n";
    }

    // Test avec quantité zéro
    $zeroQuantity = \App\Models\Product::sumPricesByQuantities($singleProduct, [1 => 0]);
    if ($zeroQuantity === 0) {
        echo "✓ Calcul avec quantité zéro réussi: {$zeroQuantity}\n";
    } else {
        echo "✗ Erreur dans le calcul avec quantité zéro: {$zeroQuantity}\n";
    }
} catch (Exception $e) {
    echo "✗ Erreur lors du test de calcul du total: " . $e->getMessage() . "\n";
}

echo "\n2. Test des getters et setters du modèle Product\n";
echo "==============================================\n";

try {
    $product = new \App\Models\Product();

    // Test des setters
    $product->setId(1);
    $product->setName('Test Product');
    $product->setDescription('Test Description');
    $product->setPrice(150);
    $product->setImage('test.jpg');
    $product->setQuantity_store(20);

    // Test des getters
    if ($product->getId() === 1) {
        echo "✓ Getter/Setter ID réussi\n";
    } else {
        echo "✗ Erreur dans le getter/setter ID\n";
    }

    if ($product->getName() === 'Test Product') {
        echo "✓ Getter/Setter Name réussi\n";
    } else {
        echo "✗ Erreur dans le getter/setter Name\n";
    }

    if ($product->getDescription() === 'Test Description') {
        echo "✓ Getter/Setter Description réussi\n";
    } else {
        echo "✗ Erreur dans le getter/setter Description\n";
    }

    if ($product->getPrice() === 150) {
        echo "✓ Getter/Setter Price réussi\n";
    } else {
        echo "✗ Erreur dans le getter/setter Price\n";
    }

    if ($product->getImage() === 'test.jpg') {
        echo "✓ Getter/Setter Image réussi\n";
    } else {
        echo "✗ Erreur dans le getter/setter Image\n";
    }

    if ($product->getQuantity_store() === 20) {
        echo "✓ Getter/Setter Quantity réussi\n";
    } else {
        echo "✗ Erreur dans le getter/setter Quantity\n";
    }
} catch (Exception $e) {
    echo "✗ Erreur lors du test des getters/setters: " . $e->getMessage() . "\n";
}

echo "\n3. Test de validation du modèle Product\n";
echo "=====================================\n";

try {
    // Test avec des données valides
    $request = new \Illuminate\Http\Request();
    $request->merge([
        'name' => 'Valid Product',
        'description' => 'Valid Description',
        'price' => 100,
        'category_id' => 1
    ]);

    // Note: Ce test nécessite une base de données configurée
    echo "⚠ Test de validation nécessite une base de données configurée\n";
    echo "  Pour tester complètement, exécutez: php artisan test\n";
} catch (Exception $e) {
    echo "✗ Erreur lors du test de validation: " . $e->getMessage() . "\n";
}

echo "\n4. Instructions pour exécuter les tests complets\n";
echo "==============================================\n";
echo "Pour exécuter tous les tests avec la base de données:\n";
echo "1. Assurez-vous que votre base de données est configurée\n";
echo "2. Exécutez: php artisan test\n";
echo "3. Ou exécutez des tests spécifiques:\n";
echo "   - php artisan test tests/Unit/ProductTotalCalculationTest.php\n";
echo "   - php artisan test tests/Feature/CartControllerTest.php\n";
echo "   - php artisan test tests/Unit/ProductModelTest.php\n";

echo "\n5. Résumé des tests créés\n";
echo "========================\n";
echo "✓ ProductTotalCalculationTest.php - Tests unitaires pour le calcul du total\n";
echo "✓ CartControllerTest.php - Tests de fonctionnalités pour le CartController\n";
echo "✓ ProductModelTest.php - Tests unitaires pour le modèle Product\n";

echo "\n=== FIN DES TESTS ===\n";
