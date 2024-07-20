<?php

namespace App\Filament\Tables\Columns; // Adjust namespace as needed

use Filament\Tables\Columns\Cell;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Views\View;

class StatusCell extends Cell
{
    public function render(): View
    {
        return View::make('filament.tables.cells.status')->with([
            'isStock' => $this->data['is_stock'], // Access data using getter-like syntax
            'isActive' => $this->data['is_active'],
            'isFeatured' => $this->data['is_featured'],
            'isOnSale' => $this->data['on_sale'],
        ]);
    }
}
