<?php

require_once 'vendor/autoload.php';

// Test simple de pagination
echo "Test de pagination Laravel\n";
echo "==========================\n\n";

// Simuler des données pour tester
$items = range(1, 25); // 25 éléments
$perPage = 10;
$currentPage = 1;

echo "Total d'éléments: " . count($items) . "\n";
echo "Éléments par page: " . $perPage . "\n";
echo "Page actuelle: " . $currentPage . "\n";

$totalPages = ceil(count($items) / $perPage);
echo "Nombre total de pages: " . $totalPages . "\n\n";

// Simuler la pagination
$offset = ($currentPage - 1) * $perPage;
$currentItems = array_slice($items, $offset, $perPage);

echo "Éléments de la page " . $currentPage . ":\n";
echo implode(', ', $currentItems) . "\n\n";

echo "Test terminé avec succès!\n";
