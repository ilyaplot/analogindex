<?php

class TestCommand extends CConsoleCommand
{
    public function beforeAction($action, $params)
    {
        date_default_timezone_set("Europe/Moscow");
        return parent::beforeAction($action, $params);
    }
}
