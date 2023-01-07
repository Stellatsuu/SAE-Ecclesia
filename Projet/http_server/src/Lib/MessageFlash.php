<?php
namespace App\SAE\Lib;

use App\SAE\Model\HTTP\Session;

class MessageFlash
{

    // Les messages sont enregistrés en session associée à la clé suivante
    private static string $cleFlash = "_messagesFlash";

    public static function ajouter(string $type, string $message): void
    {
        $session = Session::getInstance();
        $messages = static::lireTousMessages();
        $messages[] = [
            "type" => $type,
            "message" => $message
        ];
        $session->enregistrer(self::$cleFlash, $messages);
    }

    public static function contientMessage(string $type): bool
    {
        $messages = static::lireTousMessages();
        foreach ($messages as $message) {
            if (isset($message["type"]) && $message["type"] == $type) {
                return true;
            }
        }
        return false;
    }

    public static function lireMessages(string $type): array
    {
        $session = Session::getInstance();
        $messages = static::lireTousMessages();
        $messagesFiltres = [];
        foreach ($messages as $key => $value) {
            if (isset($value["type"]) && $value["type"] == $type) {
                $messagesFiltres[] = $value;
                unset($messages[$key]);
            }
        }

        $session->enregistrer(self::$cleFlash, $messages);

        return $messagesFiltres;
    }

    public static function lireTousMessages(): array
    {
        $session = Session::getInstance();
        return $session->contient(self::$cleFlash) ? $session->lire(self::$cleFlash) : [];
    }
}
