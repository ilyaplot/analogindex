<?php
class UrlManager extends CUrlManager
{
    /**
	 * Преобразует любой текст в транслит для ссылки
	 * @param string $str
	 * @return string
	 * 
	 * @example echo Yii::app()->urlManager->translitUrl("Тест"); 
	 */
	public function translitUrl($str)
	{
		$translit = transliterator_transliterate('Any-Latin; Lower()', $str);
		$link = substr(preg_replace("~[^a-z0-9_\-]+~", "-", trim($translit)), 0, 200);
        return str_replace("+", "%2B", $link);
	}
    
}