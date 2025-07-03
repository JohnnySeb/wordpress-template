<?php

add_action('init', function () {
    // Post Types
    register_extended_post_type('pci_product', [
        'menu_icon' => 'dashicons-cart',
        'supports' => ['title', 'editor', 'thumbnail'],
        'has_archive' => false,
        'labels' => [
            'singular_name' => 'Produit',
            'add_new' => 'Ajouter un nouveau produit',
            'add_new_item' => 'Ajouter un nouveau produit',
            'edit_item' => 'Modifier produit',
            'new_item' => 'Nouveau produit',
            'view_item' => 'Voir produit',
            'view_items' => 'Voir produits',
            'search_items' => 'Rechercher produits',
            'not_found' => 'Aucun produit trouvé',
            'not_found_in_trash' => 'Aucun produit trouvé dans la corbeille',
            'all_items' => 'Tous les produits',
            'archives' => 'Archives des produits',
            'attributes' => 'Attributs des produits',
            'insert_into_item' => 'Insérer dans le produit',
            'uploaded_to_this_item' => 'Téléversé sur ce produit',
            'filter_items_list' => 'Filtrer la liste des produits',
            'items_list_navigation' => 'Navigation de la liste des produits',
            'items_list' => 'Liste des produits',
        ],
    ], [
        'singular' => __('Product', 'tolle'),
        'plural' =>  __('Products', 'tolle'),
        'slug' => 'produit',
    ]);

    // Taxonomies
    register_extended_taxonomy('product_category', 'pci_product', [],
    [
        'singular' => __('Category', 'tolle'),
        'plural' => __('Categories', 'tolle'),
        'slug' => 'pci-categorie',
    ]);

    register_extended_taxonomy('brand', 'pci_product', [],
    [
        'singular' => __('Brand', 'tolle'),
        'plural' => __('Brands', 'tolle'),
        'slug' => 'marque',
    ]);

});
