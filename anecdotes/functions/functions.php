<?php

function combine($array1) {


    //echo "<pre>";print_r($array1);
    
    //echo count($array1[0]);

    $array2 = array();
    $i = 0;
    while ($i < count($array1[0])) {
        $array2[$i]["id"] = $array1[1][$i][0];
        $array2[$i]["text"] = $array1[2][$i][0];
        $array2[$i]["offset"] = $array1[1][$i][1];
        $i++;
    }

    //echo "<pre>";print_r($array2);
    return $array2;
}

function teiClean($tei) {

    $remove = array(
        '|<[pc]b([^/]+)?/>|',
        '|<anchor([^/]+)?/>|',
        '|</?quote>|',

        //   '|<note.*?</note>|',
        '|</p>|',
        '|</l>|',
        '|<p.*?>|',
        '|<l.*?>|',
    ); //reste les cas où le milestone serait enfant de hi...

    $replace = array(
        '',
        '',

        //   '',
        '',
        '',
        '',
        '<lb/>',
        '<lb/>', //les lb, c'est moche

        
    );
    return preg_replace($remove, $replace, $tei);
}

function title_transform($title) {
    $children = $title->childNodes;
    $string = "";
    foreach($children as $child){
        if($child->nodeType == XML_ELEMENT_NODE){
            $string .= "<".$child->getAttribute("rend").">".$child->textContent."</".$child->getAttribute("rend").">";
        }else{
            $string .= $child->textContent;
        }

    }

    return $string;
}
function html_txt($string){
    return preg_replace("|<[^>]+?>|", "",$string);
}
function sort_by_order($a, $b) {

    return $a['date'] - $b['date'];
}

function transform($xml) {


    //var_dump($xml);
    $dom = new DOMDocument();
    $dom->loadXML($xml);

    //var_dump($dom);
    $xslt = new DOMDocument();
    $xslt->load("functions/anecdotes.xsl");
    $proc = new XSLTProcessor();
    $proc->importStyleSheet($xslt);
    $html = $proc->transformToXML($dom);

    //echo $html;
    return $html;
}
?>