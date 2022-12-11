<?php

namespace App\SAE\Lib;

class PhotoProfil {

    public static function getRandomPhotoProfilParDefaut(): string {

        $randomInt = random_int(0, 10);

        $photoData = file_get_contents(__DIR__ . "/../../web/assets/images/defaultPFPs/default_$randomInt.png");

        return base64_encode($photoData);
    }

    public static function convertirRedimensionnerRogner(string $photoData, int $longueurCote = 256): string {

        $image = imagecreatefromstring(base64_decode($photoData));

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
}