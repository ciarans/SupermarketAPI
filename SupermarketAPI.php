<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class SupermarketAPI {

    public $url = null;
    public $name = null;
    public $price = 0.00;
    public $raw = "";

    public function curl($url) {
        $ch = curl_init();
        $agent = "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, $agent);
       // curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $data = curl_exec($ch);
        return $data;
    }
    
    
    public function get_price(){
        return (float) str_replace('£','', $this->price);
    }
    
    public function get_formatted_price(){
        $number = $this->get_price();
        return number_format($number, 2, '.', '');
    }

}

class Asda extends SupermarketAPI {

    public function __construct($sku) {
        $this->url = "http://groceries.asda.com/api/items/view?&itemid=" . $sku;
        $this->name = "ASDA";
        $this->raw = $this->curl($this->url);
        $this->price = $this->find_price();
    }

    public function find_price() {
        $data = json_decode($this->raw);        
       return $data->items[0]->price;
    }

}

class Waitrose extends SupermarketAPI {

    public function __construct($sku) {
        $this->url = "http://www.waitrose.com/shop/DisplayProductFlyout?productId=" . $sku;
        $this->name = "Waitrose";
        $this->raw = $this->curl($this->url);
        $this->price = $this->find_price();
    }

    public function find_price() {
        libxml_use_internal_errors(true);
        $doc = new DOMDocument;
        $doc->loadHTML($this->raw);
        $xpath = new DOMXPath($doc);
        foreach ($xpath->query('//p [@class="price"]') as $div) {
            return $div->nodeValue;
        }
    }

}

class Tesco extends SupermarketAPI {

    public function __construct($sku) {
        $this->url = "http://www.tesco.com/groceries/product/details/?id=" . $sku;
        $this->name = "Tesco";
        $this->raw = $this->curl($this->url);
        $this->price = $this->find_price();
    }

    public function find_price() {
        libxml_use_internal_errors(true);
        $doc = new DOMDocument;
        $doc->loadHTML($this->raw);
        $xpath = new DOMXPath($doc);
        foreach ($xpath->query('//span [@class="linePrice"]') as $div) {
            return $div->nodeValue;
        }
    }

}

class Morrisons extends SupermarketAPI {

    public function __construct($sku) {
        $this->url = "https://groceries.morrisons.com/webshop/getBackOfPackPopup.do?sku=" . $sku . "&ajax=ajax";
        $this->name = "Morrisons";
        $this->raw = $this->curl($this->url);
        $this->price = $this->find_price();
    }

    public function curl($url) {
        return file_get_contents($url);
    }

    public function find_price() {
        libxml_use_internal_errors(true);
        $doc = new DOMDocument;
        $doc->loadHTML($this->raw);
        $xpath = new DOMXPath($doc);

        foreach ($xpath->query('//meta [@itemprop="price"]/@content') as $div) {
            return $div->value;
        }
    }

}
