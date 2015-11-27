<?php

/**
 * @author Ciaran Synnott <hello@synnott.co.uk>
 * @copyright (c) 2015 - Ciaran Synnott
 * @license 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.  IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
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
