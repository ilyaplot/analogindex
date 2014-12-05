<?php

class TopicContent extends CActiveRecord
{
    public $source_url = '';

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    
    public function tableName()
    {
        return "{{topic_content}}";
    }
    
    public function getDbConnection()
    {
        return Yii::app()->teta;
    }
    
    public function afterFind()
    {
        $content = $this->topic_text_source;
        $html = phpQuery::newDocumentHtml($content);
        $p = pq($html)->find("p:last");
        if (preg_match("/<p>[\w]+: <a href=\"http:\/\/[\w]+\.&lt;.*&gt;(?P<link>.*)<\/p>/isu", $p->html(), $matches)) {
            $link = $matches['link'];
        } else {
            $link = pq($p)->find("a")->attr("href"); 
        }
        if (!empty($link) && (preg_match("/.*Источник:.*/isu", $p->html()) || preg_match("/.*Source:.*/isu", $p->html()))) {
            pq($html)->find("p:last")->remove();
            pq($html)->find("p:empty")->remove();
            $this->topic_text = (string) $html;
            $link = str_replace("http://http//", "http//", $link);
            
            if (preg_match("/^.*[\.=]$/isu", $link)) {
                if (preg_match("/&gt;(?P<link>.*)<\/p>/isu", $p, $matches)) {
                    $link = $matches['link'];
                } else {
                    echo $link.PHP_EOL;
                    exit();
                }
            }
            $ch = curl_init($link);
            
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_setopt($ch, CURLOPT_AUTOREFERER, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_exec($ch);

            if (curl_errno($ch) && !in_array(curl_errno($ch), [28,56]) ) {
                echo "Curl error #".curl_errno($ch)." " . curl_error($ch) . PHP_EOL;
                echo $link.PHP_EOL;
                echo (string) $p.PHP_EOL;
                return false;
            } else {
                $link = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
                $link = preg_replace("/(.*)\?utm_source=.*/isu", "$1", $link);
                $link = preg_replace("/(.*)#.*/isu", "$1", $link);
            }
            curl_close($ch);
            $this->source_url = $link;
        } else {
            return false;
        }
        
        return parent::afterFind();
    }
    
    /**
     * Заглушка на сохранение записи
     * @return boolean
     */
    public function beforeSave()
    {
        return false;
    }
}