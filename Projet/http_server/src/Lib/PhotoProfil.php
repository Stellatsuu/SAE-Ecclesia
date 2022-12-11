<?php

namespace App\SAE\Lib;

class PhotoProfil {

    public static function getRandomPhotoProfilParDefaut(): string {

        $randomInt = random_int(0, 10);

        $photoData = file_get_contents(__DIR__ . "/../../web/assets/images/defaultPFPs/default_$randomInt.png");

        return bin2hex($photoData);
    }

}