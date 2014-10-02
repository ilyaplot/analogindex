<?php
class BootstrapLinkPager extends CLinkPager
{
    const CSS_SELECTED_PAGE='active';
    
    public function run()
    {
            $this->registerClientScript();
            $buttons=$this->createPageButtons();
            if(empty($buttons))
                    return;
            echo $this->header;
            echo CHtml::openTag("div", $this->htmlOptions);
            echo CHtml::tag('ul',array(),implode("\n",$buttons));
            echo CHtml::closeTag("div");
            echo $this->footer;
    }
}
