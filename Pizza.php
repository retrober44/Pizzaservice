<?php 


class Pizza{

    private $id;
    private $pName;
    private $pPreis;
    private $pStatus;

    function __construct($i, $n, $p, $s)
    {
        $this->id = $i;
        $this->pName = $n;
        $this->pPreis = $p;
        $this->pStatus =$s;
    }

    public function getId(){
        return $this->id;
    }

    public function getPizzaName(){
        return $this->pName;
    }

    public function getPizzaPreis(){
        return $this->pPreis;
    }

    public function getPizzaStatus(){
        return $this->pStatus;
    }

    public function setPizzaStatus($s){
        $this->pStatus = $s;
    }



}

?>