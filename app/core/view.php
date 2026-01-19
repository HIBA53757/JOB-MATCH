<?php
namespace app\core;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class View
{



     private static ?Environment $twig = null;

    public static function render(string $template, array $data = []): void
    {
        if (self::$twig === null) {
            $view = __DIR__ . '/../views';
            $loader = new FilesystemLoader($view);

            self::$twig = new Environment($loader, [
                'cache' => false, // __DIR__ . '/../../storage/cache' in prod
                'debug' => true,
            ]);
        }

        echo self::$twig->render($template . '.twig', $data);
    }

    
}

