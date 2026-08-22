<?php

$categories = [
    [
        "slug" => "power-tools-trade-supplies",
        "name" => "Power Tools & Trade Supplies",
        "tagline" => "Fit out job sites, fast.",
        "description" => "Cordless tools, fixings, workwear and site consumables sourced from vetted manufacturers, palletised and ready for trade accounts of any size.",
        "gradient" => "cat-gradient-1",
        "photo" => "assets/images/photos/category-power-tools-trade-supplies.jpg",
        "stat" => ["label" => "SKUs listed", "value" => "1,200+"],
    ],
    [
        "slug" => "beauty-personal-care",
        "name" => "Beauty & Personal Care",
        "tagline" => "Shelf-ready ranges, from formula to face.",
        "description" => "Skincare, haircare and grooming lines with compliant labelling and flexible case sizes — ideal for salons, pharmacies and online resellers.",
        "gradient" => "cat-gradient-2",
        "photo" => "assets/images/photos/category-beauty-personal-care.jpg",
        "stat" => ["label" => "Active brands", "value" => "60+"],
    ],
    [
        "slug" => "grocery-fmcg",
        "name" => "Grocery & FMCG",
        "tagline" => "Pantry staples, moved at pace.",
        "description" => "Ambient, chilled-stable and packaged goods with reliable lead times, built for independent stores and larger distribution networks alike.",
        "gradient" => "cat-gradient-3",
        "photo" => "assets/images/photos/category-grocery-fmcg.jpg",
        "stat" => ["label" => "Pallets shipped monthly", "value" => "3,400"],
    ],
    [
        "slug" => "home-lifestyle",
        "name" => "Home & Lifestyle",
        "tagline" => "Everyday goods, well made.",
        "description" => "Kitchenware, textiles and small furnishings curated for retail shelves and DTC storefronts, with consistent quality control at every batch.",
        "gradient" => "cat-gradient-4",
        "photo" => "assets/images/photos/category-home-lifestyle.jpg",
        "stat" => ["label" => "New lines per quarter", "value" => "45"],
    ],
];

function get_category_by_slug(array $categories, string $slug): ?array
{
    foreach ($categories as $category) {
        if ($category["slug"] === $slug) {
            return $category;
        }
    }
    return null;
}
