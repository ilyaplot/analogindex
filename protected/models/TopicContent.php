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
        
        if (empty($content)) {
            file_put_contents("/var/www/analogindex/logs/news_empty_content.txt", $this->topic_id.PHP_EOL, FILE_APPEND);
            return parent::afterFind();
        }
        
        $html = phpQuery::newDocumentHtml($content);
        
        
        $p = pq($html)->find("p:last");
        if (empty($p)) {
            file_put_contents("/var/www/analogindex/logs/news_empty_paragraph.txt", $this->topic_id.PHP_EOL, FILE_APPEND);
            echo "EMPTY PARAGPAPH!".PHP_EOL;
            return false;
        }
        unset ($content);
        
        if (preg_match("/<p>[\w]+: <a href=\"http:\/\/[\w]+\.&lt;.*&gt;(?P<link>.*)<\/p>/isu", pq($p)->html(), $matches)) {
            $link = $matches['link'];
            unset($matches);
        } else {
            
            $link = @pq($p)->find("a")->attr("href"); 
        }
        
        if (!empty($link) && (preg_match("/.*Источник:.*/isu", $p->html()) || preg_match("/.*Source:.*/isu", $p->html()))) {
            pq($html)->find("p:last")->remove();
            pq($html)->find("p:empty")->remove();
            $this->topic_text = (string) $html;
            unset($html);
            
            $link = str_replace("http://http//", "http//", $link);
            
            if (preg_match("/^.*[\.=]$/isu", $link)) {
                if (preg_match("/&gt;(?P<link>.*)<\/p>/isu", $p, $matches)) {
                    $link = $matches['link'];
                    unset($matches);
                } else {
                    file_put_contents("/var/www/analogindex/logs/news_empty_source.txt", $this->topic_id.PHP_EOL, FILE_APPEND);
                    return false;
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
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_exec($ch);

            if (curl_errno($ch) && !in_array(curl_errno($ch), [28,56,47,35]) ) {
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
            unset ($ch, $link);
        } else {
            file_put_contents("/var/www/analogindex/logs/news_empty_source.txt", $this->topic_id.PHP_EOL, FILE_APPEND);
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