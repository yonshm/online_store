<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Item;

class Product extends Model
{
    /**
     * PRODUCT ATTRIBUTES
     * $this->attributes['id'] - int - contains the product primary key (id)
     * $this->attributes['name'] - string - contains the product name
     * $this->attributes['description'] - string - contains the product description
     * $this->attributes['image'] - string - contains the product image
     * $this->attributes['price'] - int - contains the product price
     * $this->attributes['created_at'] - timestamp - contains the product creation date
     * $this->attributes['updated_at'] - timestamp - contains the product update date
     * $this->items - Item[] - contains the associated items
     */
    protected $fillable = [
        'name',
        'description',
        'image',
        'price',
        'category_id',
        'quantity_store',
        'items',
        'supplier_id'
    ];
    public static function validate($request)
    {
        $request->validate([
            "name" => "required|max:255",
            "description" => "required",
            "price" => "required|numeric|gt:0",
            'image' => 'image',
            'category_id' => 'required|exists:categories,id'
        ]);
    }

    public static function sumPricesByQuantities($products, $productsInSession)
    {
        $total = 0;
        foreach ($products as $product) {
            $total = $total + ($product->getPrice()*$productsInSession[$product->getId()]);
        }

        return $total;
    }

    public function getId()
    {
        return $this->attributes['id'];
    }

    public function setId($id)
    {
        $this->attributes['id'] = $id;
    }

    public function getName()
    {
        return $this->attributes['name'];
    }

    public function setName($name)
    {
        $this->attributes['name'] = $name;
    }
    
    public function getDescription()
    {
        return $this->attributes['description'];
    }

    public function setDescription($description)
    {
        $this->attributes['description'] = $description;
    }
    
    public function getImage()
    {
        return $this->attributes['image'];
    }

    public function setImage($image)
    {
        $this->attributes['image'] = $image;
    }
    
    public function getPrice()
    {
        return $this->attributes['price'];
    }

    public function setPrice($price)
    {
        $this->attributes['price'] = $price;
    }
    public function getCategory()
    {
        return $this->category ? $this->category->name : null;
    }
    public function setCategoryId($category)
    {
        $this->attributes['category_id'] = $category;
    }

    function getQuantity_store(){
        return $this->attributes['quantity_store'];
    }

    function setQuantity_store($quantity_store){
        $this->attributes['quantity_store'] = $quantity_store;
    }

    public function getCreatedAt()
    {
        return $this->attributes['created_at'];
    }
    
    public function setCreatedAt($createdAt)
    {
        $this->attributes['created_at'] = $createdAt;
    }

    public function getUpdatedAt()
    {
        return $this->attributes['updated_at'];
    }

    public function setUpdatedAt($updatedAt)
    {
        $this->attributes['updated_at'] = $updatedAt;
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }
    
    public function getItems()
    {
        return $this->items;
    }

    public function setItems($items)
    {
        $this->items = $items;
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    
    public function discounts()
{
    return $this->hasMany(Discount::class);
}

public function getDiscountedPrice()
{
    $now = now();
    $globalDiscounts = Discount::where('type', 'global')
        ->where('start_date', '<=', $now)
        ->where('end_date', '>=', $now)
        ->get();
    $relevantDiscounts = Discount::where(function ($query) {
                $query->where('type', 'category')
                      ->where('category_id', $this->category_id)
                      ->orWhere(function ($q) {
                          $q->where('type', 'product')
                            ->where('product_id', $this->id);
                      });
            })
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->get();
     $activeDiscounts = $globalDiscounts->merge($relevantDiscounts);

        $discountTotal = $activeDiscounts->sum('discount_percentage');
        $discountTotal = min($discountTotal, 90);
        $originalPrice = $this->getPrice();
        $finalPrice = round($originalPrice * (1 - $discountTotal / 100), 2);

    return $finalPrice;
}

public function supplier()
{
    return $this->belongsTo(Supplier::class, 'supplier_id');
}

}
