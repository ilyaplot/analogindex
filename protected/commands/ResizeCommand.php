<?php

class ResizeCommand extends CConsoleCommand
{

    public function actionIndex()
    {
        ArticlesImages::model()->createPreviews();
    }

}
