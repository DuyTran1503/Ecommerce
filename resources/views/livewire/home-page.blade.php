<div>
    @include('livewire.home.banner')
    @include('livewire.home.brand', ['brands' => $brands, 'categories' => $categories])
    @include('livewire.home.category')
    @include('livewire.home.customer')
</div>
