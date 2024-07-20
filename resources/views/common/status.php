<div class="flex justify-center items-center gap-2">
    <x-filament-toggle-column :name="'is_stock'" :label="'Stock'" :tooltip="'Click to change stock status'" :extra-attributes="['class' => '!justify-center']" :value="$isStock" />
    <x-filament-toggle-column :name="'is_active'" :label="'Active'" :tooltip="'Click to change active status'" :extra-attributes="['class' => '!justify-center']" :value="$isActive" />
    <x-filament-toggle-column :name="'is_featured'" :label="'Featured'" :tooltip="'Click to change featured status'" :extra-attributes="['class' => '!justify-center']" :value="$isFeatured" />
    <x-filament-toggle-column :name="'on_sale'" :label="'On Sale'" :tooltip="'Click to change sale status'" :extra-attributes="['class' => '!justify-center']" :value="$isOnSale" />
</div>