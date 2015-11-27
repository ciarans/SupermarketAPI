# SupermarketAPI
Get the current price of an Item from Asda, Waitrose, Tesco and Morrisons

#About
SupermarketAPI was built out of frustration from trying compare prices and the big Supermarkets not having open APIs.

#Example

The example below is an example of calling IDs for Bombay sapphire.

```php
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
```
#Result
The example above will give the following results
```raw
    Tesco - 22.00
    ASDA - 20.00
    Waitrose - 21.00
    Morrisons - 22.00
```
