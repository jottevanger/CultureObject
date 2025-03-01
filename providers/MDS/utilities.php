<?php 
function findSpectrumData($array, &$results) {
    if (!is_array($array)) {
        return;
    }

    foreach ($array as $item) {
        if (is_array($item)) {
            // Check if this item has the required properties
            if (isset($item['type']) && 
                isset($item['label']) && 
                isset($item['value']) && 
                str_starts_with($item['type'], 'spectrum/')) {

                $results[] = [
                    'label' => $item['label'],
                    'type' => $item['type'],
                    'value' => $item['value']
                ];
            }

            // If there's a units array, search through it
            if (isset($item['units'])) {
                findSpectrumData($item['units'], $results);
            }

            // Continue searching through other arrays
            findSpectrumData($item, $results);
        }
    }
}

function filterArrayByKeyValue($array, $key, $keyValue)
{
//    var_dump($array);
//    die;
    return array_values(array_filter($array, function ($var) use ($key,$keyValue) {
        return ($var[$key] == $keyValue);
    }));
}

function fieldByType($arr,$type){
    return $arr["type"]==$type;
}

function collapseArray($arr = [], $sep=", ", $key = "value"){  //get values from an array of type/label/value arrays and join the target (defaults to value) with a separator
    $op = "";
    $sep2 = "";
    foreach($arr as $tlv){
        $op .= $tlv[$key].$sep2; 
        $sep2=$sep;        
    }
    return $op;
}


?>