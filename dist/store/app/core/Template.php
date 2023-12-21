<?php
if (!defined("site_title")) {
    header( 'HTTP/1.0 403 Forbidden', TRUE, 403);
    exit;
}

use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;
use Twig\TemplateWrapper;
use Twig\TwigFilter;

class Template extends FilesystemLoader {

    public function __construct($paths = [], $rootPath = null) {
        parent::__construct($paths, $rootPath);
    }

    private $cache_enabled = true;

    /**
     * @param $path
     * @return TemplateWrapper|null
     */
    public function load($path) {
        try {
            $twig = new \Twig\Environment($this, !$this->cache_enabled ? [] : ['cache' => 'app/cache']);

            $twig->addFunction(new \Twig\TwigFunction('time', function () {
                return time();
            }));

            $twig->addFunction(new \Twig\TwigFunction('url', function ($string, $internal = true) {
                return $internal ? web_root . $string : $string;
            }));

            $twig->addFunction(new \Twig\TwigFunction('img', function ($string, $internal = true) {
                return $internal ? web_root . $string : $string;
            }));

            $twig->addFunction(new \Twig\TwigFunction('stylesheet', function ($string) {
                return web_root.'public/css/' . $string . '';
            }));

            $twig->addFunction(new \Twig\TwigFunction('javascript', function ($string) {
                return web_root . 'public/js/' . $string . '';
            }));

            $twig->addFunction(new \Twig\TwigFunction('css', 
                function ($string) { return $this->getCss($string); }, 
                [ 
                    'is_safe' => ['html']
                ]
            ));

            $twig->addFunction(new \Twig\TwigFunction('js', 
                function ($string) { return $this->getJs($string); }, 
                [ 
                    'is_safe' => ['html']
                ]
            ));

            $twig->addFunction(new \Twig\TwigFunction('title', 
                function ($string) {return '<title>' . $string . '</title>'; }, 
                [ 
                    'is_safe' => ['html']
                ]
            ));
            
            $twig->addFunction(new \Twig\TwigFunction('constant', function ($string) {
                return constant($string);
            }));

            $twig->addFunction(new \Twig\TwigFunction('curdate', function ($string) {
                return date($string);
            }));

            $twig->addFunction(new \Twig\TwigFunction('debugArr', function ($string) {
                return json_encode($string, JSON_PRETTY_PRINT);
            }));

            $twig->addFunction(new \Twig\TwigFunction('in_array', function ($needle, $haystack) {
                return in_array($needle, $haystack);
            }));

            $twig->addFunction(new \Twig\TwigFunction('friendlyTitle', function ($title) {
                return Functions::friendlyTitle($title);
            }));
            
            $twig->addFilter(new TwigFilter('array_chunk', function($array, $limit) {
                return array_chunk($array, $limit);
            }));

            return $twig->load($path . '.twig');
        } catch (LoaderError|RuntimeError|SyntaxError $e) {
            return null;
        }
    }

    public function setCacheEnabled($val) {
        $this->cache_enabled = $val;
    }

    private function getCss($string) {
        return '<link rel="stylesheet" type="text/css" href="'.web_root.'public/css/' . $string . '">';
    }

    private function getjs($string) {
        return '<script type="text/javascript" src="'.web_root.'public/js/' . $string . '"></script>';
    }
}