<?php

namespace App\SAE\Model\DataObject;

use App\SAE\Controller\MainController;

abstract class AbstractDataObject {

    public abstract function formatTableau(): array;

    public abstract function getValeurClePrimaire();

    public static abstract function castIfNotNull($object, $errorUrl = "frontController.php", $errorMessage = "[OBJECT] n'existe pas");

    public static function castToClassIfNotNull($object, $className, $errorUrl = "frontController.php", $errorMessage = "[OBJECT] n'existe pas") : ?AbstractDataObject {
        str_replace("[OBJECT]", $className, $errorMessage);

        if($object == null) {
            MainController::error($errorUrl, $errorMessage);
        } else if($object instanceof $className) {
            return $object;
        } else {
            $class = get_class($object);
            throw new \Exception("Erreur: Tentative de cast de $class en $className", 1);
        }
        return null;
    }
}