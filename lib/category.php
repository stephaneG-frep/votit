<?php


function getCategories(PDO $pdo): array
{
    $query = $pdo->query("SELECT * FROM category");
    $query->execute();
    return $query->fetchAll();
}