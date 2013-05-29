<?php 

/**
 * Reverses an array without using array_reverse and returns the reversed array.
 * @param array $arr
 * @return array
 */
function sf_array_reverse( array $arr ){
    $res = array();
     
    $length = count($arr); 

    for($i = 0; $i < $length; $i++){
    	array_unshift($res,$arr[$i]);
    }
    return $res;
}

/**
 * Returns an array with the elements from $arr that are evenly divisible by $divisor
 * @param array $arr
 * @param integer $divisor
 * @return array
 */
function sf_evenly_divisble( array $arr, $divisor ){
    $res = array();
    
    foreach($arr as $element){
    	if($element%$divisor===0)
    	array_push($res,$element);
    }
    return $res;
}

/**
 * Returns a function (closure) which returns the sum of $l and $r.
 * @return function
 */
function sf_get_sum_closure( $l, $r ){     
	return function() use ($l,$r) {
		return $l + $r;
	};
}


/**
 * Given a chunk of HTML in $html,
 * Find all the <a> tags, extract the href="" links and return them in an array
 */
function sf_extract_links($html){    
    $res = array();      
    
    $doc = new DOMDocument();
    $doc->loadHTML($html);
    $doc->saveHTML();

    $domnodelist = $doc->getElementsByTagName('a');

    for ($i = 0; $i < $domnodelist->length; ++$i) {
        $el = $domnodelist->item($i);
        $attr = $el->getAttribute('href');
        $res[] = $attr;
    }

    return $res;
}