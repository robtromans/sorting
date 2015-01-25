<?php

function bubbleSort($items) {
    $n = count($items) - 1;
    do {
        $swapped = false;
        for ($i = 0; $i < $n; $i++) {
            if ($items[$i] > $items[$i+1]) {
                $x = $items[$i];
                $items[$i] = $items[$i+1];
                $items[$i+1] = $x;
                $swapped = true;
            }
        }
    } while($swapped);

    return $items;
}

$sorted = bubbleSort([7,4,5,3,8,9,0,1,6,2]);

print_r($sorted);