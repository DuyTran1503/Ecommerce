<?php

namespace App\Livewire;

use App\Helpers\CartManagement;
use App\Livewire\Partials\Navbar;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Jantinnerezo\LivewireAlert\LivewireAlert;

#[Title('Products page - Septenary Solution')]
class ProductsPage extends Component
{
    use WithPagination;
    use LivewireAlert;
    #[Url]
    public $selected_categories = [];

    #[Url]
    public $selected_brands = [];


    #[Url]
    public $price = 1000000000;
    public $sort = 'latest';
    // Thêm vào giỏ hàng

    public function addToCart($product_id)
    {
        $total_count = CartManagement::addItemToCart($product_id);
        $this->dispatch('update-cart-count', $total_count)->to(Navbar::class);
        $this->alert('success', 'Thêm giỏ hàng thành công', [
            'position' => 'top',
            'timer' => 2500,
            'toast' => true,
        ]);
    }

    public function render()
    {
        $query = Product::query()->where("is_active", 1);

        if (!empty($this->selected_categories)) {
            $query->whereIn("category_id", $this->selected_categories);
        }

        if (!empty($this->selected_brands)) {
            $query->whereIn("brand_id", $this->selected_brands);
        }

        if ($this->price) {
            $query->whereBetween('price', [0, $this->price]);
        }
        if ($this->sort == 'latest') {
            $query->latest();
        }
        if ($this->sort == 'price') {
            $query->orderBy('price');
        }
        $products = $query->paginate(10);

        $categories = Category::query()->where("is_active", 1)->get();
        $brands = Brand::where('is_active', 1)->get();

        return view('livewire.products-page', compact('products', 'categories', 'brands'));
    }
}
