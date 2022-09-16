<?php
namespace Core;

class View  // this class render views html file
{
    public static function render($view, $args = []) //takes a path to a folder Views and takes an array "arg"
        // with variables (which we want to pass to the view)
    {
        extract($args, EXTR_SKIP);//

        $file = VIEW_DIR . $view . '.php';

        if (!is_readable($file)) {//if the file is not available throws an error
            throw new \Exception("[{$file}] not found", 404);
        }

        require $file;//if file is available open it
    }
}
