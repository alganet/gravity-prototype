<?php

namespace Supercluster\Gravity\MediaTypes;

use Twig_Environment;

/**
 * Represents data as HTML
 */
class TextHtml
{
    public function __construct(Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    public function __invoke($data)
    {
        header('Content-Type: text/html');
        return $this->twig->render(
            "index.twig",
            $data
        );
    }
}
