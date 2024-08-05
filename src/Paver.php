<?php

namespace Jeffreyvr\Paver;

use Jeffreyvr\Paver\Api;
use Jeffreyvr\Paver\View;
use Jeffreyvr\Paver\Frame;
use Jeffreyvr\Paver\Editor;
use Jeffreyvr\Paver\Blocks\BlockFactory;

class Paver
{
    private static $instance = null;

    public string|array $viewPath = __DIR__ . '/../resources/views/';

    public string|array $assetPath = __DIR__ . '/../assets/';

    public Editor $editor;

    public Frame $frame;

    public Api $api;

    public array $blocks = [];

    static function instance()
    {
        if (static::$instance === null) {
            static::$instance = new static();

            static::$instance->editor = new Editor();
            static::$instance->frame = new Frame();
            static::$instance->api = new Api();
        }

        return static::$instance;
    }

    function viewPath()
    {
        return $this->viewPath;
    }

    function assetPath()
    {
        return $this->assetPath;
    }

    function loadAssetContent($path)
    {
        $assetPaths = (array) $this->assetPath;

        foreach ($assetPaths as $assetPath) {
            if (file_exists($assetPath . $path)) {
                return file_get_contents($assetPath . $path);
            }
        }

        throw new \Exception("Asset file {$path} not found.");
    }

    function api()
    {
        return $this->api;
    }

    function blocks()
    {
        return $this->blocks;
    }

    function getBlock($name, $instance = false)
    {
        if (! isset($this->blocks[$name])) {
            throw new \Exception("Block {$name} not found.");
        }

        return $instance
            ? BlockFactory::createById($name)
            : $this->blocks[$name];
    }

    function registerBlock($class)
    {
        $this->blocks[$class::$reference] = $class;

        return $this;
    }

    function render(array|string $content = [])
    {
        if(is_string($content)) {
            $content = json_decode($content, true);
        }

        return new View(paver()->viewPath() . 'editor.php', [
            'content' => addslashes(json_encode($content)),
            'editorHtml' => (new View(paver()->viewPath() . 'frame.php', [
                'blocks' => $content
            ]))->render()
        ]);
    }
}
