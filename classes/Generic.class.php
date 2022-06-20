<?php
class Generic {
    //muutujad ehk omadused (properties)
    private $secretValue = 13;
    public $justValue = 7;
    private $sentValue;

    //funktsioonid ehk meetodid (methods)
    function __construct($receivedValue){
        echo "klass alustab!";
        $this->sentValue = $receivedValue;
    }

    function __destruct(){
        echo "Klassiga on kõik!";
    }

    //private - meetod mõledud mingiteks töödeks klassi sees
    private function multiply(){
        echo "Salajaste arvude korrutiseks on: " .$this->secretValue * $this->sentValue;
    }

    public function reveal(){
        echo "Salajased arvud on: " .$this->secretValue ." ja " .$this->sentValue;
    }
}