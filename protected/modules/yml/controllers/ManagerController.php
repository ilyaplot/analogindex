<?php


class ManagerController extends CController
{
    public function actionIndex()
    {
        $source = new YmlSources();
        $source->name = 'Test';
        $source->url = 'ya.ru';
        $source->save();
    }
}