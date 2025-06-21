<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class CartControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $category;
    protected $supplier;

    protected function setUp(): void
    {
        parent::setUp();

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
     * Test d'affichage du panier vide
     */
    public function test_cart_index_with_empty_cart()
    {
        $response = $this->actingAs($this->user)
            ->get('/cart');

        $response->assertStatus(200);
        $response->assertViewIs('cart.index');
        $response->assertViewHas('viewData');
        $response->assertViewHas('viewData.total', 0);
        $response->assertViewHas('viewData.products', []);
    }

    /**
     * Test d'affichage du panier avec des produits
     */
    public function test_cart_index_with_products()
    {
        // Créer des produits de test
        $product1 = Product::create([
            'name' => 'Test Product 1',
            'description' => 'Test Description 1',
            'price' => 100,
            'image' => 'test1.jpg',
            'category_id' => $this->category->id,
            'quantity_store' => 10,
            'supplier_id' => $this->supplier->id
        ]);

        $product2 = Product::create([
            'name' => 'Test Product 2',
            'description' => 'Test Description 2',
            'price' => 200,
            'image' => 'test2.jpg',
            'category_id' => $this->category->id,
            'quantity_store' => 5,
            'supplier_id' => $this->supplier->id
        ]);

        // Simuler des produits dans la session
        $sessionData = [
            $product1->id => 2, // 2 quantités
            $product2->id => 1  // 1 quantité
        ];

        $response = $this->actingAs($this->user)
            ->withSession(['products' => $sessionData])
            ->get('/cart');

        $response->assertStatus(200);
        $response->assertViewIs('cart.index');
        $response->assertViewHas('viewData');
        $response->assertViewHas('viewData.total', 400); // (100 * 2) + (200 * 1) = 400
        $response->assertViewHas('viewData.products');

        $products = $response->viewData('viewData.products');
        $this->assertCount(2, $products);
    }

    /**
     * Test d'ajout d'un produit au panier
     */
    public function test_add_product_to_cart()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 150,
            'image' => 'test.jpg',
            'category_id' => $this->category->id,
            'quantity_store' => 10,
            'supplier_id' => $this->supplier->id
        ]);

        $response = $this->actingAs($this->user)
            ->post("/cart/add/{$product->id}", [
                'quantity' => 3
            ]);

        $response->assertRedirect('/cart');
        $this->assertSessionHas('products');

        $sessionProducts = session('products');
        $this->assertArrayHasKey($product->id, $sessionProducts);
        $this->assertEquals(3, $sessionProducts[$product->id]);
    }

    /**
     * Test d'ajout d'un produit avec quantité supérieure au stock
     */
    public function test_add_product_with_quantity_exceeding_stock()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 150,
            'image' => 'test.jpg',
            'category_id' => $this->category->id,
            'quantity_store' => 5, // Stock limité à 5
            'supplier_id' => $this->supplier->id
        ]);

        $response = $this->actingAs($this->user)
            ->post("/cart/add/{$product->id}", [
                'quantity' => 10 // Quantité supérieure au stock
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'La quantité demandée dépasse le stock disponible.');
    }

    /**
     * Test d'ajout d'un produit inexistant
     */
    public function test_add_nonexistent_product_to_cart()
    {
        $response = $this->actingAs($this->user)
            ->post('/cart/add/999', [
                'quantity' => 1
            ]);

        $response->assertStatus(404);
    }

    /**
     * Test de suppression du panier
     */
    public function test_delete_cart()
    {
        // Ajouter des produits au panier d'abord
        $product = Product::create([
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 150,
            'image' => 'test.jpg',
            'category_id' => $this->category->id,
            'quantity_store' => 10,
            'supplier_id' => $this->supplier->id
        ]);

        $this->actingAs($this->user)
            ->post("/cart/add/{$product->id}", ['quantity' => 2]);

        // Vérifier que le panier contient des produits
        $this->assertSessionHas('products');

        // Supprimer le panier
        $response = $this->actingAs($this->user)
            ->get('/cart/delete');

        $response->assertRedirect();
        $this->assertSessionMissing('products');
    }

    /**
     * Test d'achat avec panier vide
     */
    public function test_purchase_with_empty_cart()
    {
        $response = $this->actingAs($this->user)
            ->get('/cart/purchase');

        $response->assertRedirect('/cart');
    }

    /**
     * Test d'achat avec des produits dans le panier
     */
    public function test_purchase_with_products_in_cart()
    {
        // Créer des produits de test
        $product1 = Product::create([
            'name' => 'Test Product 1',
            'description' => 'Test Description 1',
            'price' => 100,
            'image' => 'test1.jpg',
            'category_id' => $this->category->id,
            'quantity_store' => 10,
            'supplier_id' => $this->supplier->id
        ]);

        $product2 = Product::create([
            'name' => 'Test Product 2',
            'description' => 'Test Description 2',
            'price' => 200,
            'image' => 'test2.jpg',
            'category_id' => $this->category->id,
            'quantity_store' => 5,
            'supplier_id' => $this->supplier->id
        ]);

        // Ajouter des produits au panier
        $sessionData = [
            $product1->id => 2,
            $product2->id => 1
        ];

        $response = $this->actingAs($this->user)
            ->withSession(['products' => $sessionData])
            ->get('/cart/purchase');

        $response->assertStatus(200);
        $response->assertViewIs('cart.purchase');
        $response->assertViewHas('viewData');
        $response->assertViewHas('viewData.order');

        // Vérifier que le panier a été vidé
        $this->assertSessionMissing('products');

        // Vérifier que les stocks ont été mis à jour
        $product1->refresh();
        $product2->refresh();
        $this->assertEquals(8, $product1->quantity_store); // 10 - 2
        $this->assertEquals(4, $product2->quantity_store); // 5 - 1

        // Vérifier que le solde utilisateur a été déduit
        $this->user->refresh();
        $this->assertEquals(600, $this->user->balance); // 1000 - 400
    }

    /**
     * Test d'ajout de produit avec quantité invalide
     */
    public function test_add_product_with_invalid_quantity()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 150,
            'image' => 'test.jpg',
            'category_id' => $this->category->id,
            'quantity_store' => 10,
            'supplier_id' => $this->supplier->id
        ]);

        $response = $this->actingAs($this->user)
            ->post("/cart/add/{$product->id}", [
                'quantity' => 0 // Quantité invalide
            ]);

        $response->assertRedirect('/cart');
        $this->assertSessionMissing('products');
    }

    /**
     * Test d'accès au panier sans authentification
     */
    public function test_cart_access_without_authentication()
    {
        $response = $this->get('/cart');
        $response->assertRedirect('/login');
    }

    /**
     * Test d'ajout de produit sans authentification
     */
    public function test_add_product_without_authentication()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 150,
            'image' => 'test.jpg',
            'category_id' => $this->category->id,
            'quantity_store' => 10,
            'supplier_id' => $this->supplier->id
        ]);

        $response = $this->post("/cart/add/{$product->id}", [
            'quantity' => 1
        ]);

        $response->assertRedirect('/login');
    }
}
