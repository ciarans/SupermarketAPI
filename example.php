<?php
        include(APPPATH . "SupermarketAPI.php");
        
        $bombay_sapphire_1l = (object) array(
            "tesco" => 252695240,
            "asda" => 512843,
            "waitrose" => 34657,
            "morrisons" => 217561011
        );

        $t = new Tesco($bombay_sapphire_1l->tesco);
        echo $t->name . " - " . $t->get_formatted_price() . "\n";

        $a = new Asda($bombay_sapphire_1l->asda);
        echo $a->name . " - " . $a->get_formatted_price() . "\n";

        $w = new Waitrose($bombay_sapphire_1l->waitrose);
        echo $w->name . " - " . $w->get_formatted_price() . "\n";

        $m = new Morrisons($bombay_sapphire_1l->morrisons);
        echo $m->name . " - " . $m->get_formatted_price();
