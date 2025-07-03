<?php

use Extended\ACF\Location;
use Extended\ACF\Fields\Text;

add_action('acf/init', function () {
    register_extended_field_group([
        'title' => 'Informations d\'un produit',
        'fields' => [
            Text::make('Identifiant PCI', 'pci_id')->disabled(),
            Text::make('Code du format', 'code_format')->disabled(),
            Text::make('Description du format', 'format_description'),
            Text::make('Date de modification', 'modification_date')->disabled(),
        ],
        'location' => [
            Location::where('post_type', 'pci_product')
        ],
    ]);
});
