<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use App\Models\Supplier;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class CartCookieOptimizationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $category;
    protected $supplier;
    protected $cartService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cartService = app(CartService::class);

        // Créer un utilisateur de test
        $this->user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'balance' => 1000
        ]);

        // Créer une catégorie de test
        $this->category = Category::create([
            'name' => 'Electronics'
        ]);

        // Créer un fournisseur de test
        $this->supplier = Supplier::create([
            'raison_social' => 'Test Supplier',
            'adresse' => '123 Test Street',
            'telephone' => '1234567890',
            'email' => 'supplier@test.com'
        ]);
    }

    /**
     * Test d'ajout d'un produit au panier avec cookies
     */
    public function test_add_product_to_cart_with_cookies()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 100,
            'image' => 'test.jpg',
            'category_id' => $this->category->id,
            'quantity_store' => 10,
            'supplier_id' => $this->supplier->id
        ]);

        $response = $this->post("/cart/add/{$product->id}", [
            'quantity' => 2
        ]);

        $response->assertRedirect('/cart');

        // Vérifier que le cookie a été créé
        $this->assertTrue($response->headers->has('Set-Cookie'));

        // Vérifier le contenu du cookie
        $cookieValue = $response->headers->get('Set-Cookie');
        $this->assertStringContainsString('shopping_cart', $cookieValue);
    }

    /**
     * Test de récupération du panier depuis les cookies
     */
    public function test_get_cart_from_cookies()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 100,
            'image' => 'test.jpg',
            'category_id' => $this->category->id,
            'quantity_store' => 10,
            'supplier_id' => $this->supplier->id
        ]);

        // Créer un cookie avec des données de panier
        $cartData = json_encode([$product->id => 3]);

        $response = $this->withCookie('shopping_cart', $cartData)
            ->get('/cart');

        $response->assertStatus(200);
        $response->assertViewHas('viewData.total', 300); // 100 * 3
        $response->assertViewHas('viewData.itemCount', 3);
    }

    /**
     * Test de mise à jour d'un produit dans le panier
     */
    public function test_update_product_in_cart()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 100,
            'image' => 'test.jpg',
            'category_id' => $this->category->id,
            'quantity_store' => 10,
            'supplier_id' => $this->supplier->id
        ]);

        // Créer un cookie avec des données de panier
        $cartData = json_encode([$product->id => 2]);

        $response = $this->withCookie('shopping_cart', $cartData)
            ->put("/cart/update/{$product->id}", [
                'quantity' => 5
            ]);

        $response->assertRedirect('/cart');

        // Vérifier que le cookie a été mis à jour
        $this->assertTrue($response->headers->has('Set-Cookie'));
    }

    /**
     * Test de suppression d'un produit du panier
     */
    public function test_remove_product_from_cart()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 100,
            'image' => 'test.jpg',
            'category_id' => $this->category->id,
            'quantity_store' => 10,
            'supplier_id' => $this->supplier->id
        ]);

        // Créer un cookie avec des données de panier
        $cartData = json_encode([$product->id => 2]);

        $response = $this->withCookie('shopping_cart', $cartData)
            ->delete("/cart/remove/{$product->id}");

        $response->assertRedirect('/cart');

        // Vérifier que le cookie a été mis à jour
        $this->assertTrue($response->headers->has('Set-Cookie'));
    }

    /**
     * Test de vidage du panier
     */
    public function test_clear_cart()
    {
        $response = $this->get('/cart/delete');

        $response->assertRedirect('/cart');

        // Vérifier que le cookie a été supprimé
        $cookieValue = $response->headers->get('Set-Cookie');
        $this->assertStringContainsString('shopping_cart=deleted', $cookieValue);
    }

    /**
     * Test de l'API pour obtenir le nombre d'articles
     */
    public function test_cart_count_api()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 100,
            'image' => 'test.jpg',
            'category_id' => $this->category->id,
            'quantity_store' => 10,
            'supplier_id' => $this->supplier->id
        ]);

        // Créer un cookie avec des données de panier
        $cartData = json_encode([$product->id => 3]);

        $response = $this->withCookie('shopping_cart', $cartData)
            ->get('/api/cart/count');

        $response->assertStatus(200);
        $response->assertJson([
            'count' => 3,
            'isEmpty' => false
        ]);
    }

    /**
     * Test de l'API pour obtenir les détails du panier
     */
    public function test_cart_details_api()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 100,
            'image' => 'test.jpg',
            'category_id' => $this->category->id,
            'quantity_store' => 10,
            'supplier_id' => $this->supplier->id
        ]);

        // Créer un cookie avec des données de panier
        $cartData = json_encode([$product->id => 2]);

        $response = $this->withCookie('shopping_cart', $cartData)
            ->get('/api/cart/details');

        $response->assertStatus(200);
        $response->assertJson([
            'total' => 200,
            'itemCount' => 2,
            'isEmpty' => false
        ]);
    }

    /**
     * Test de migration des sessions vers les cookies
     */
    public function test_session_to_cookie_migration()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 100,
            'image' => 'test.jpg',
            'category_id' => $this->category->id,
            'quantity_store' => 10,
            'supplier_id' => $this->supplier->id
        ]);

        // Simuler des données de session
        $sessionData = [$product->id => 2];

        $response = $this->withSession(['products' => $sessionData])
            ->get('/cart');

        $response->assertStatus(200);

        // Vérifier que le cookie a été créé
        $this->assertTrue($response->headers->has('Set-Cookie'));

        // Vérifier que la session a été vidée
        $this->assertNull(session('products'));
    }

    /**
     * Test de persistance du panier entre les sessions
     */
    public function test_cart_persistence_between_sessions()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 100,
            'image' => 'test.jpg',
            'category_id' => $this->category->id,
            'quantity_store' => 10,
            'supplier_id' => $this->supplier->id
        ]);

        // Créer un cookie avec des données de panier
        $cartData = json_encode([$product->id => 2]);

        // Première requête
        $response1 = $this->withCookie('shopping_cart', $cartData)
            ->get('/cart');

        $response1->assertStatus(200);
        $response1->assertViewHas('viewData.total', 200);

        // Deuxième requête (simule une nouvelle session)
        $response2 = $this->withCookie('shopping_cart', $cartData)
            ->get('/cart');

        $response2->assertStatus(200);
        $response2->assertViewHas('viewData.total', 200);
    }

    /**
     * Test de validation du stock avec les cookies
     */
    public function test_stock_validation_with_cookies()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 100,
            'image' => 'test.jpg',
            'category_id' => $this->category->id,
            'quantity_store' => 5, // Stock limité
            'supplier_id' => $this->supplier->id
        ]);

        // Essayer d'ajouter plus que le stock disponible
        $response = $this->post("/cart/add/{$product->id}", [
            'quantity' => 10
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    /**
     * Test de performance avec les cookies
     */
    public function test_cart_performance_with_cookies()
    {
        $startTime = microtime(true);

        $response = $this->get('/cart');

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        // Le temps d'exécution devrait être inférieur à 1 seconde
        $this->assertLessThan(1.0, $executionTime);

        $response->assertStatus(200);
    }
}
