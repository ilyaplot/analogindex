<?php


class ManagerController extends CController
{
    public function actionIndex()
    {
        $my_data = array(
            array(
                'text'     => 'Node 1',
                'expanded' => true, // будет развернута ветка или нет (по умолчанию)
                    'children' => array(
                         array(
                            'text'     => 'Node 1.1',
                         ),   
                         array(
                            'text'     => 'Node 1.2',
                         ),   
                         array(
                            'text'     => 'Node 1.3',
                         ),             
                    )
            ),
        );

        $this->widget('CTreeView', array('data' => $my_data));
    }
}