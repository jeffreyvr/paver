<?php

namespace Jeffreyvr\Paver;

use Jeffreyvr\Paver\Blocks\BlockFactory;

class Paver
{
    private static $instance = null;

    public string|array $viewPath = __DIR__.'/../resources/views/';

    public string|array $assetPath = __DIR__.'/../assets/';

    public string|array $languagePath = __DIR__.'/../lang/';

    public string $locale = 'en';

    public Editor $editor;

    public Frame $frame;

    public Api $api;

    public array $blocks = [];

    public bool $alpine = true;

    public bool $debug = false;

    public static function instance()
    {
        if (static::$instance === null) {
            static::$instance = new static;

            static::$instance->editor = new Editor;
            static::$instance->frame = new Frame;
            static::$instance->api = new Api;
        }

        return static::$instance;
    }

    public function debug($flag)
    {
        $this->debug = $flag;

        return $this;
    }

    public function alpine($flag)
    {
        $this->alpine = $flag;

        return $this;
    }

    public function viewPath()
    {
        return $this->viewPath;
    }

    public function assetPath()
    {
        return $this->assetPath;
    }

    public function loadAssetContent($path)
    {
        $assetPaths = (array) $this->assetPath;

        foreach ($assetPaths as $assetPath) {
            if (file_exists($assetPath.$path)) {
                return file_get_contents($assetPath.$path);
            }
        }

        throw new \Exception("Asset file {$path} not found.");
    }

    public function getLocalizations()
    {
        if (file_exists($this->languagePath.$this->locale.'.json')) {
            return json_decode(file_get_contents($this->languagePath.$this->locale.'.json'), true);
        }

        return [];
    }

    public function api()
    {
        return $this->api;
    }

    public function blocks($encode = false, $withInstance = false): string|array
    {
        $blocks = array_map(function ($block) use($withInstance) {
            $instance = BlockFactory::createById($block);

            $data = [
                'name' => $instance->name,
                'reference' => $instance::$reference,
                'icon' => $instance->getIcon(),
                'childOnly' => $instance->childOnly,
            ];

            if($withInstance) {
                $data['instance'] = $instance;
            }

            return $data;
        }, array_keys($this->blocks));

        return $encode ? json_encode($blocks) : $blocks;
    }

    public function getBlock($name, $instance = false)
    {
        if (! isset($this->blocks[$name])) {
            throw new \Exception("Block {$name} not found.");
        }

        return $instance
            ? BlockFactory::createById($name)
            : $this->blocks[$name];
    }

    public function registerBlock($class)
    {
        $this->blocks[$class::$reference] = $class;

        return $this;
    }

    public function render(array|string|null $content = null, array $config = [])
    {
        if ($content === null) {
            $content = [];
        } elseif (is_string($content)) {
            $content = json_decode($content, true);
        }

        foreach (paver()->blocks(withInstance: true) as $block) {
            $block['instance']->beforeEditorRender();
        }

        $data = [
            'content' => addslashes(json_encode($content)),

            'editorHtml' => (new View(paver()->viewPath().'frame.php', [
                'blocks' => $content,
                'api' => paver()->api(),
            ]))->render(),

            'config' => array_merge([
                'debug' => $this->debug,
            ], $config),
        ];

        return new View(paver()->viewPath().'editor.php', $data);
    }
}
