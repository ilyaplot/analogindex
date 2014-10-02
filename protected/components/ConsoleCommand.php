<?php
class ConsoleCommand extends CConsoleCommand
{
    public function Log($message, $exit=false)
    {
        echo PHP_EOL.date("Y-m-d H:i:s ").$message;
        if ($exit) exit();
    }
}