<?php
Yii::import("application.components.DGSphinxSearch.DGSphinxSearch");
class SphinxSearch extends DGSphinxSearch
{
    public function escape($string)
    {
        return $this->client->EscapeString($string);
    }
}
