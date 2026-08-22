<?php

/**
 * Real brands Armadio stocks. "moq" is Armadio's own example order-quantity
 * policy for that brand's range — adjust to your actual terms. "origin" is
 * the brand's publicly known country of origin.
 */

$brand_categories = [
    "All",
    "Power Tools & Trade Supplies",
    "Beauty & Personal Care",
    "Grocery & FMCG",
    "Home & Lifestyle",
];

$brands = [
    [
        "slug" => "dewalt",
        "name" => "DeWalt",
        "category" => "Power Tools & Trade Supplies",
        "origin" => "United States",
        "moq" => "50 units",
        "description" => "Professional-grade power tools and site equipment, trusted on job sites worldwide.",
        "logo" => "assets/images/brands/dewalt.jpg",
    ],
    [
        "slug" => "makita",
        "name" => "Makita",
        "category" => "Power Tools & Trade Supplies",
        "origin" => "Japan",
        "moq" => "50 units",
        "description" => "Cordless and corded power tools engineered for daily professional trade use.",
        "logo" => "assets/images/brands/makita.jpg",
    ],
    [
        "slug" => "milwaukee",
        "name" => "Milwaukee",
        "category" => "Power Tools & Trade Supplies",
        "origin" => "United States",
        "moq" => "50 units",
        "description" => "Heavy-duty power tools and accessories built for demanding job sites.",
        "logo" => "assets/images/brands/milwaukee.jpg",
    ],
    [
        "slug" => "loreal",
        "name" => "L'Oréal",
        "category" => "Beauty & Personal Care",
        "origin" => "France",
        "moq" => "24 units",
        "description" => "World-leading beauty group spanning skincare, haircare and cosmetics.",
        "logo" => "assets/images/brands/loreal.png",
    ],
    [
        "slug" => "bioderma",
        "name" => "Bioderma",
        "category" => "Beauty & Personal Care",
        "origin" => "France",
        "moq" => "24 units",
        "description" => "French dermo-cosmetic skincare developed alongside dermatologists.",
        "logo" => "assets/images/brands/bioderma.png",
    ],
    [
        "slug" => "joico",
        "name" => "JOICO",
        "category" => "Beauty & Personal Care",
        "origin" => "United States",
        "moq" => "24 units",
        "description" => "Professional haircare and colour systems used in salons worldwide.",
        "logo" => "assets/images/brands/joico.png",
    ],
    [
        "slug" => "lee-kum-kee",
        "name" => "Lee Kum Kee",
        "category" => "Grocery & FMCG",
        "origin" => "Hong Kong",
        "moq" => "1 pallet",
        "description" => "Iconic Asian sauces and condiments, a kitchen staple since 1888.",
        "logo" => "assets/images/brands/lee-kum-kee.png",
    ],
    [
        "slug" => "mae-ploy",
        "name" => "Mae Ploy",
        "category" => "Grocery & FMCG",
        "origin" => "Thailand",
        "moq" => "1 pallet",
        "description" => "Authentic Thai curry pastes, coconut milk and sauces.",
        "logo" => "assets/images/brands/mae-ploy.png",
    ],
    [
        "slug" => "natco",
        "name" => "NATCO",
        "category" => "Grocery & FMCG",
        "origin" => "United Kingdom",
        "moq" => "1 pallet",
        "description" => "British-Asian grocery staples — spices, pulses and pantry essentials.",
        "logo" => "assets/images/brands/natco.png",
    ],
    [
        "slug" => "trs",
        "name" => "TRS",
        "category" => "Grocery & FMCG",
        "origin" => "United Kingdom",
        "moq" => "1 pallet",
        "description" => "Asia's Finest Foods — spices, rice and grocery staples for South Asian cooking.",
        "logo" => "assets/images/brands/trs.png",
    ],
];

function get_brand_by_slug(array $brands, string $slug): ?array
{
    foreach ($brands as $brand) {
        if ($brand["slug"] === $slug) {
            return $brand;
        }
    }
    return null;
}
