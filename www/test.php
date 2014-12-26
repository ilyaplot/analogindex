<?php
$content = file_get_contents(dirname(__FILE__)."/phonearena.html");
$html = phpQuery::newDocumentHTML($content);
$fullspecs = pq($html)->find("div.s_specs_box");
foreach ($fullspecs as $specs) {
    echo pq($specs)->html().PHP_EOL;
}

