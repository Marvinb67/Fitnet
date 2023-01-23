<?php

namespace App\Data;

class SearchData
{
    public function __construct(private string $q = '', private $categories = [], private int $max, private int $min, private $promo )
    {
        
    }
    //https://www.google.com/search?client=firefox-b-d&q=symfony+filtre+produit#fpstate=ive&vld=cid:e64549d9,vid:4uYpFjfUUbc
}