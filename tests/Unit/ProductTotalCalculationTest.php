<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductTotalCalculationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test de calcul du total avec un seul produit
     */
    public function test_calculate_total_single_product()
    {
        // Créer un produit de test
        $product = new Product();
        $product->setId(1);
        $product->setPrice(100);
        $product->setName('Test Product');

        $products = collect([$product]);
        $productsInSession = [1 => 2]; // 2 quantités pour le produit ID 1

        $total = Product::sumPricesByQuantities($products, $productsInSession);

        $this->assertEquals(200, $total);
    }

    /**
     * Test de calcul du total avec plusieurs produits
     */
    public function test_calculate_total_multiple_products()
    {
        // Créer plusieurs produits de test
        $product1 = new Product();
        $product1->setId(1);
        $product1->setPrice(50);
        $product1->setName('Product 1');

        $product2 = new Product();
        $product2->setId(2);
        $product2->setPrice(75);
        $product2->setName('Product 2');

        $product3 = new Product();
        $product3->setId(3);
        $product3->setPrice(25);
        $product3->setName('Product 3');

        $products = collect([$product1, $product2, $product3]);
        $productsInSession = [
            1 => 2, // 2 quantités pour le produit 1
            2 => 1, // 1 quantité pour le produit 2
            3 => 3  // 3 quantités pour le produit 3
        ];

        $total = Product::sumPricesByQuantities($products, $productsInSession);

        // Calcul attendu: (50 * 2) + (75 * 1) + (25 * 3) = 100 + 75 + 75 = 250
        $this->assertEquals(250, $total);
    }

    /**
     * Test de calcul du total avec quantité zéro
     */
    public function test_calculate_total_with_zero_quantity()
    {
        $product = new Product();
        $product->setId(1);
        $product->setPrice(100);
        $product->setName('Test Product');

        $products = collect([$product]);
        $productsInSession = [1 => 0]; // 0 quantité

        $total = Product::sumPricesByQuantities($products, $productsInSession);

        $this->assertEquals(0, $total);
    }

    /**
     * Test de calcul du total avec prix décimal
     */
    public function test_calculate_total_with_decimal_prices()
    {
        $product1 = new Product();
        $product1->setId(1);
        $product1->setPrice(19.99);
        $product1->setName('Product 1');

        $product2 = new Product();
        $product2->setId(2);
        $product2->setPrice(29.50);
        $product2->setName('Product 2');

        $products = collect([$product1, $product2]);
        $productsInSession = [
            1 => 2, // 2 quantités pour le produit 1
            2 => 1  // 1 quantité pour le produit 2
        ];

        $total = Product::sumPricesByQuantities($products, $productsInSession);

        // Calcul attendu: (19.99 * 2) + (29.50 * 1) = 39.98 + 29.50 = 69.48
        $this->assertEquals(69.48, $total);
    }

    /**
     * Test de calcul du total avec un seul produit et une quantité
     */
    public function test_calculate_total_single_product_single_quantity()
    {
        $product = new Product();
        $product->setId(1);
        $product->setPrice(150);
        $product->setName('Test Product');

        $products = collect([$product]);
        $productsInSession = [1 => 1]; // 1 quantité

        $total = Product::sumPricesByQuantities($products, $productsInSession);

        $this->assertEquals(150, $total);
    }

    /**
     * Test de calcul du total avec des produits non trouvés dans la session
     */
    public function test_calculate_total_with_missing_session_products()
    {
        $product1 = new Product();
        $product1->setId(1);
        $product1->setPrice(100);
        $product1->setName('Product 1');

        $product2 = new Product();
        $product2->setId(2);
        $product2->setPrice(200);
        $product2->setName('Product 2');

        $products = collect([$product1, $product2]);
        $productsInSession = [1 => 2]; // Seulement le produit 1 dans la session

        $total = Product::sumPricesByQuantities($products, $productsInSession);

        // Le produit 2 n'est pas dans la session, donc il ne devrait pas être calculé
        // Résultat attendu: 100 * 2 = 200
        $this->assertEquals(200, $total);
    }

    /**
     * Test de calcul du total avec des produits vides
     */
    public function test_calculate_total_with_empty_products()
    {
        $products = collect([]);
        $productsInSession = [];

        $total = Product::sumPricesByQuantities($products, $productsInSession);

        $this->assertEquals(0, $total);
    }

    /**
     * Test de calcul du total avec des quantités négatives (cas d'erreur)
     */
    public function test_calculate_total_with_negative_quantities()
    {
        $product = new Product();
        $product->setId(1);
        $product->setPrice(100);
        $product->setName('Test Product');

        $products = collect([$product]);
        $productsInSession = [1 => -2]; // Quantité négative

        $total = Product::sumPricesByQuantities($products, $productsInSession);

        // Le calcul devrait fonctionner même avec des quantités négatives
        // Résultat attendu: 100 * (-2) = -200
        $this->assertEquals(-200, $total);
    }
}
