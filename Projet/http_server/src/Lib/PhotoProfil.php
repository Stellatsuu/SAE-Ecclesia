<?php

namespace App\SAE\Lib;

class PhotoProfil {

    public static function getRandomPhotoProfilParDefaut(): string {

        $randomInt = random_int(0, 10);

        $photoData = file_get_contents(__DIR__ . "/../../web/assets/images/defaultPFPs/default_$randomInt.png");

        return base64_encode($photoData);
    }

    public static function getPhotoProfilNull(): string {

        $photoData = file_get_contents(__DIR__ . "/../../web/assets/images/defaultPFPs/null.jpg");

        return base64_encode($photoData);
    }

    public static function getPhotoProfilDeleted(): string {

        $photoData = file_get_contents(__DIR__ . "/../../web/assets/images/defaultPFPs/deleted.jpg");

        return base64_encode($photoData);
    }

    public static function convertirRedimensionnerRogner(string $photoData, int $longueurCote = 256): string {

        $b64 = base64_decode($photoData);

        //if the image is an svg, return it
        if (strpos($b64, "<svg") !== false) {
            return $photoData;
        }

        $image = imagecreatefromstring($b64);

        $longueur = imagesx($image);
        $hauteur = imagesy($image);

        // crop the largest possible square in the center of the image
        if ($longueur > $hauteur) {
            $x = ($longueur - $hauteur) / 2;
            $y = 0;
            $longueur = $hauteur;
        } else {
            $x = 0;
            $y = ($hauteur - $longueur) / 2;
            $hauteur = $longueur;
        }
        $image = imagecrop($image, ['x' => $x, 'y' => $y, 'width' => $longueur, 'height' => $hauteur]);

        // resize the image to the desired size
        $image = imagescale($image, $longueurCote, $longueurCote);
        ob_start();
        imagepng($image);
        $photoData = ob_get_contents();
        ob_end_clean();
        imagedestroy($image);

        return base64_encode($photoData);
    }

    public static function getBaliseImg(string $photoData, string $alt = "", string $class = ""): string {

        $decoded = base64_decode($photoData);
        $isSvg = strpos($decoded, "<svg") !== false;


        if($isSvg) {
            return "<img src='data:image/svg+xml;base64," . $photoData . "' alt='$alt' class='$class'>";
        } else {
            return "<img src='data:image/png;base64," . $photoData . "' alt='$alt' class='$class'>";
        }

    }

}