<?php


namespace Yoda\traits;

trait FormatObjectTrait
{
    public function formatThis($object)
    {
        echo '<pre>'; print_r($object); echo '</pre>';
    }

}