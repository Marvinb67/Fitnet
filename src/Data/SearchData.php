<?php

namespace App\Data;

class SearchData
{
    /**
     * haine de caractéres à chercher
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
     * Numéro de la page acctuel par défault 1
     * @var integer
     */
    private ?int $page = 1;

    /**
     * Nombre de pages
     *
     * @var integer
     */
    private ?int $nbPages = 0;

    /**
     * Tableau des resultas final
     *
     * @var array
     */
    private ?array $result = [];

    //https://www.google.com/search?client=firefox-b-d&q=symfony+filtre+produit#fpstate=ive&vld=cid:e64549d9,vid:4uYpFjfUUbc

    public function getQ(): ?string { return $this->q; }
    public function setQ(?string $q): self { $this->q = $q; return $this; }

    public function getAmis(){ return $this->Amis; }
    public function setAmis($Amis): self { $this->Amis = $Amis; return $this; }

    public function getDates(){ return $this->dates; }

    public function getPage(){ return $this->page; }
    public function setPage($page): self { $this->page = $page; return $this; }

    public function getResult(){ return $this->result; }
    public function setResult($result): self { $this->result = $result; return $this; }

    public function getNbPages(){ return $this->nbPages; }
    public function setNbPages($nbPages): self { $this->nbPages = $nbPages; return $this; }
}