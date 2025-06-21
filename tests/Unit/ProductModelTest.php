<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Discount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class ProductModelTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $category;
    protected $supplier;

    protected function setUp(): void
    {
        parent::setUp();

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
     * Test de création d'un produit
     */
    public function test_can_create_product()
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

        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals('Test Product', $product->getName());
        $this->assertEquals('Test Description', $product->getDescription());
        $this->assertEquals(100, $product->getPrice());
        $this->assertEquals('test.jpg', $product->getImage());
        $this->assertEquals(10, $product->getQuantity_store());
        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'price' => 100
        ]);
    }

    /**
     * Test des getters et setters du produit
     */
    public function test_product_getters_and_setters()
    {
        $product = new Product();

        // Test des setters
        $product->setId(1);
        $product->setName('Test Product');
        $product->setDescription('Test Description');
        $product->setPrice(150);
        $product->setImage('test.jpg');
        $product->setCategoryId($this->category->id);
        $product->setQuantity_store(20);

        // Test des getters
        $this->assertEquals(1, $product->getId());
        $this->assertEquals('Test Product', $product->getName());
        $this->assertEquals('Test Description', $product->getDescription());
        $this->assertEquals(150, $product->getPrice());
        $this->assertEquals('test.jpg', $product->getImage());
        $this->assertEquals($this->category->id, $product->getCategoryId());
        $this->assertEquals(20, $product->getQuantity_store());
    }

    /**
     * Test de la relation avec la catégorie
     */
    public function test_product_category_relationship()
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

        $this->assertInstanceOf(Category::class, $product->category);
        $this->assertEquals('Electronics', $product->category->name);
        $this->assertEquals('Electronics', $product->getCategory());
    }

    /**
     * Test de la relation avec le fournisseur
     */
    public function test_product_supplier_relationship()
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

        $this->assertInstanceOf(Supplier::class, $product->supplier);
        $this->assertEquals('Test Supplier', $product->supplier->raison_social);
    }

    /**
     * Test de validation du produit
     */
    public function test_product_validation()
    {
        $request = new \Illuminate\Http\Request();

        // Test avec des données valides
        $request->merge([
            'name' => 'Valid Product',
            'description' => 'Valid Description',
            'price' => 100,
            'category_id' => $this->category->id
        ]);

        // La validation ne devrait pas lever d'exception
        try {
            Product::validate($request);
            $this->assertTrue(true); // Validation réussie
        } catch (\Exception $e) {
            $this->fail('La validation aurait dû réussir');
        }
    }

    /**
     * Test de validation avec des données invalides
     */
    public function test_product_validation_with_invalid_data()
    {
        $request = new \Illuminate\Http\Request();

        // Test avec des données invalides
        $request->merge([
            'name' => '', // Nom vide
            'description' => '', // Description vide
            'price' => -10, // Prix négatif
            'category_id' => 999 // Catégorie inexistante
        ]);

        $this->expectException(\Illuminate\Validation\ValidationException::class);
        Product::validate($request);
    }

    /**
     * Test de calcul du prix avec remise
     */
    public function test_get_discounted_price()
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

        // Créer une remise globale de 20%
        Discount::create([
            'name' => 'Global Discount',
            'description' => '20% off everything',
            'discount_percentage' => 20,
            'type' => 'global',
            'start_date' => now()->subDay(),
            'end_date' => now()->addDay()
        ]);

        $discountedPrice = $product->getDiscountedPrice();

        // Prix original: 100, remise: 20% = 80
        $this->assertEquals(80, $discountedPrice);
    }

    /**
     * Test de calcul du prix avec remise de catégorie
     */
    public function test_get_discounted_price_with_category_discount()
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

        // Créer une remise de catégorie de 15%
        Discount::create([
            'name' => 'Category Discount',
            'description' => '15% off electronics',
            'discount_percentage' => 15,
            'type' => 'category',
            'category_id' => $this->category->id,
            'start_date' => now()->subDay(),
            'end_date' => now()->addDay()
        ]);

        $discountedPrice = $product->getDiscountedPrice();

        // Prix original: 100, remise: 15% = 85
        $this->assertEquals(85, $discountedPrice);
    }

    /**
     * Test de calcul du prix avec remise de produit
     */
    public function test_get_discounted_price_with_product_discount()
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

        // Créer une remise de produit de 25%
        Discount::create([
            'name' => 'Product Discount',
            'description' => '25% off this product',
            'discount_percentage' => 25,
            'type' => 'product',
            'product_id' => $product->id,
            'start_date' => now()->subDay(),
            'end_date' => now()->addDay()
        ]);

        $discountedPrice = $product->getDiscountedPrice();

        // Prix original: 100, remise: 25% = 75
        $this->assertEquals(75, $discountedPrice);
    }

    /**
     * Test de calcul du prix avec plusieurs remises
     */
    public function test_get_discounted_price_with_multiple_discounts()
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

        // Créer une remise globale de 10%
        Discount::create([
            'name' => 'Global Discount',
            'description' => '10% off everything',
            'discount_percentage' => 10,
            'type' => 'global',
            'start_date' => now()->subDay(),
            'end_date' => now()->addDay()
        ]);

        // Créer une remise de catégorie de 15%
        Discount::create([
            'name' => 'Category Discount',
            'description' => '15% off electronics',
            'discount_percentage' => 15,
            'type' => 'category',
            'category_id' => $this->category->id,
            'start_date' => now()->subDay(),
            'end_date' => now()->addDay()
        ]);

        $discountedPrice = $product->getDiscountedPrice();

        // Prix original: 100, remise totale: 25% = 75
        $this->assertEquals(75, $discountedPrice);
    }

    /**
     * Test de calcul du prix sans remise
     */
    public function test_get_discounted_price_without_discount()
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

        $discountedPrice = $product->getDiscountedPrice();

        // Sans remise, le prix devrait être le même
        $this->assertEquals(100, $discountedPrice);
    }

    /**
     * Test de la limite maximale de remise (90%)
     */
    public function test_get_discounted_price_with_maximum_discount()
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

        // Créer une remise de 95% (devrait être limitée à 90%)
        Discount::create([
            'name' => 'High Discount',
            'description' => '95% off',
            'discount_percentage' => 95,
            'type' => 'global',
            'start_date' => now()->subDay(),
            'end_date' => now()->addDay()
        ]);

        $discountedPrice = $product->getDiscountedPrice();

        // Prix original: 100, remise limitée à 90% = 10
        $this->assertEquals(10, $discountedPrice);
    }

    /**
     * Test de mise à jour du stock
     */
    public function test_update_stock()
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

        // Mettre à jour le stock
        $product->setQuantity_store(5);
        $product->save();

        $this->assertEquals(5, $product->getQuantity_store());
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'quantity_store' => 5
        ]);
    }

    /**
     * Test de la relation avec les items
     */
    public function test_product_items_relationship()
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

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $product->getItems());
        $this->assertCount(0, $product->getItems()); // Aucun item au début
    }
}
