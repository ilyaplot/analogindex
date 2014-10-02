<?php
class LinkPager extends CLinkPager
{
    const CSS_FIRST_PAGE='first';
    const CSS_LAST_PAGE='last';
    const CSS_PREVIOUS_PAGE='pag_prev';
    const CSS_NEXT_PAGE='pag_next';
    const CSS_INTERNAL_PAGE='page';
    const CSS_HIDDEN_PAGE='hidden';
    const CSS_SELECTED_PAGE='active';
    
    public function run()
    {
            $this->registerClientScript();
            $buttons=$this->createPageButtons();
            if(empty($buttons))
                    return;
            echo CHtml::openTag("nav", $this->htmlOptions);
            echo CHtml::tag('ul',array('class'=>'nav-to-left', 'id'=>'pagination'),implode("\n",$buttons));
            echo CHtml::closeTag("nav");
            echo $this->footer;
    }
    
    
    /**
     * Creates a page button.
     * You may override this method to customize the page buttons.
     * @param string $label the text label for the button
     * @param integer $page the page number
     * @param string $class the CSS class for the page button.
     * @param boolean $hidden whether this page button is visible
     * @param boolean $selected whether this page button is selected
     * @return string the generated button
     */
    protected function createPageButton($label,$page,$class,$hidden,$selected)
    {
            if($hidden || $selected)
                    $class.=' '.($hidden ? $this->hiddenPageCssClass : $this->selectedPageCssClass);
            return CHtml::link($label,$this->createPageUrl($page), array('class'=>$class));
    }
}
