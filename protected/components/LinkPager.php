<?php
/**
 * <ul class="pagination">
				        		<li class="waves-effect pagination-btn"><a href="#!"><i class="mdi-av-fast-rewind"></i></a></li>
							    <li class="disabled pagination-btn"><a href="#!"><i class="mdi-navigation-chevron-left"></i></a></li>
							    <li class="hide-on-med-and-down active"><a href="#!">1</a></li>
							    <li class="hide-on-med-and-down waves-effect"><a href="#!">2</a></li>
							    <li class="hide-on-med-and-down waves-effect"><a href="#!">3</a></li>
							    <li class="hide-on-med-and-down waves-effect"><a href="#!">4</a></li>
							    <li class="hide-on-med-and-down waves-effect"><a href="#!">5</a></li>
							    <li class="waves-effect pagination-btn"><a href="#!"><i class="mdi-navigation-chevron-right"></i></a></li>
							    <li class="waves-effect pagination-btn"><a href="#!"><i class="mdi-av-fast-forward"></i></a></li>
							</ul>
 */
class LinkPager extends CLinkPager
{
    const CSS_FIRST_PAGE='first';
    const CSS_LAST_PAGE='last';
    const CSS_PREVIOUS_PAGE='pag_prev';
    const CSS_NEXT_PAGE='pag_next';
    const CSS_INTERNAL_PAGE='page';
    const CSS_HIDDEN_PAGE='disabled';
    const CSS_SELECTED_PAGE='active';
    
    public function run()
    {
            $this->selectedPageCssClass = self::CSS_SELECTED_PAGE;
            $this->registerClientScript();
            $buttons=$this->createPageButtons();
            if(empty($buttons))
                    return;
            echo CHtml::tag('ul',array('class'=>'pagination'),implode("\n",$buttons));
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
            $class='waves-effect pagination-btn';
            if($hidden || $selected)
                $class.=' '.($hidden ? $this->hiddenPageCssClass : $this->selectedPageCssClass);
            $html = CHtml::openTag('li',['class'=>$class]);
            $html.= CHtml::link(' '.$label.' ',$this->createPageUrl($page));
            return $html.CHtml::closeTag('li');
    }
}
