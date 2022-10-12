<?php

namespace App\Traits;

trait ImageTrait {
    public function saveImage($image)
    {
        $extention = $image->guessExtension();
        $name = time().".".$extention;
        $image->move("images", $name);
        return $name;
    }
}