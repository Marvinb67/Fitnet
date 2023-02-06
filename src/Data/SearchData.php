<?php

namespace App\Data;

class SearchData
{
    /**
     *
     * @var string
     */

    private ?string $q = null; 

    /**
     *
     * @var array
     */
    private $Amis = []; 

    /**
     *
     * @var array
     */
    private $dates = []; 

    /**
     *
     * @var integer
     */
    private $page = 1;


    //https://www.google.com/search?client=firefox-b-d&q=symfony+filtre+produit#fpstate=ive&vld=cid:e64549d9,vid:4uYpFjfUUbc

    public function getQ(): ?string { return $this->q; }
    public function setQ(?string $q): self { $this->q = $q; return $this; }

    public function getAmis(){ return $this->Amis; }
    public function setAmis($Amis): self { $this->Amis = $Amis; return $this; }

    public function getDates(){ return $this->dates; }

    public function getPage(){ return $this->page; }
    public function setPage($page): self { $this->page = $page; return $this; }


}