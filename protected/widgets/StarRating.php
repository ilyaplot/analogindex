<?php
class StarRating extends CStarRating
{
    public function registerClientScript($id)
    {
        $jsOptions=$this->getClientOptions();
        $jsOptions=empty($jsOptions) ? '' : CJavaScript::encode($jsOptions);
        $js="$('#{$id} > input').rating({$jsOptions});";
        $cs=Yii::app()->getClientScript();
        $cs->registerCoreScript('rating');
        $cs->registerScript('Yii.CStarRating#'.$id,$js);
        if($this->cssFile!==false)
            self::registerCssFile($this->cssFile);
    }
}