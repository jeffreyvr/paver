<?php

/*
 * Test page for option asset auto-loading.
 *
 * Both blocks below use the same RichText option, which declares jQuery and
 * Summernote via $this->script() / $this->style(). Paver should output those
 * tags once each, with jQuery before Summernote.
 *
 * Run: npm run start-server, then open http://localhost:8000/assets.php
 */

use Jeffreyvr\Paver\Blocks\Block;
use Jeffreyvr\Paver\Blocks\Options\Option;
use Jeffreyvr\Paver\Paver;

require __DIR__ . '/../../vendor/autoload.php';

class RichText extends Option
{
    public function __construct(
        public string $label,
        public string $name,
        public array $config = []
    ) {
        $this->script('jquery', 'https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js');
        $this->script('summernote', 'https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js', ['jquery']);
        $this->style('summernote', 'https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css');
    }

    public function render(): string
    {
        $config = array_merge([
            'height' => 200,
            'dialogsInBody' => true,
        ], $this->config);

        $configJson = htmlentities(json_encode($config), ENT_QUOTES, 'UTF-8');

        return <<<HTML
            <div x-data="{
                init() {
                    jQuery(this.\$refs.editor).summernote({
                        ...JSON.parse(JSON.stringify({$configJson})),
                        callbacks: {
                            onChange: (contents) => { {$this->name} = contents; },
                            onInit: () => {
                                jQuery(this.\$refs.editor).summernote('code', {$this->name} || '');
                            }
                        }
                    });
                }
            }">
                <div class="paver__option">
                    <label>{$this->label}</label>
                    <textarea x-ref="editor"></textarea>
                </div>
            </div>
        HTML;
    }
}

abstract class RichTextBlock extends Block
{
    public array $data = [
        'content' => '',
    ];

    public function options()
    {
        return [
            RichText::make('Content', 'content'),
        ];
    }

    public function render()
    {
        $content = $this->data['content'] ?? '';

        if (trim($content) === '' && $this->isInEditor()) {
            $content = '<p style="color: #9ca3af; padding: 15px;">Empty — click, then hit the pencil to edit…</p>';
        }

        return '<div>'.$content.'</div>';
    }
}

class Intro extends RichTextBlock
{
    public string $name = 'Intro';

    public static string $reference = 'assets_demo.intro';
}

class Outro extends RichTextBlock
{
    public string $name = 'Outro';

    public static string $reference = 'assets_demo.outro';
}

$paver = Paver::instance();

$paver->api->setEndpoint('assets.php?api');

// Both blocks share the same option, so its assets should be deduped.
$paver->registerBlock(Intro::class);
$paver->registerBlock(Outro::class);

$content = [
    ['block' => 'assets_demo.intro', 'data' => ['content' => '<p>Edit me to check the option works.</p>']],
];

// The handler exits the request, so blocks must be registered above this line.
if (isset($_GET['api'])) {
    Jeffreyvr\Paver\Endpoints\Handler::run();
}
?>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<body class="flex h-screen">

<?php echo $paver->render($content); ?>

<div id="asset-check" style="position:fixed;bottom:12px;left:12px;z-index:9999;background:#111;color:#eee;font:12px/1.6 ui-monospace,monospace;padding:12px 16px;border-radius:8px;max-width:360px">
    checking…
</div>

<script>
    window.addEventListener('load', () => {
        const srcs = Array.from(document.querySelectorAll('script[src]')).map(s => s.src);
        const links = Array.from(document.querySelectorAll('link[rel=stylesheet]')).map(l => l.href);

        const jqIndex = srcs.findIndex(s => s.includes('jquery'));
        const snIndex = srcs.findIndex(s => s.includes('summernote'));

        const checks = [
            ['jQuery tag present', jqIndex !== -1],
            ['Summernote tag present', snIndex !== -1],
            ['Summernote CSS present', links.some(l => l.includes('summernote'))],
            ['jQuery before Summernote', jqIndex !== -1 && snIndex !== -1 && jqIndex < snIndex],
            ['Summernote JS loaded once (deduped)', srcs.filter(s => s.includes('summernote')).length === 1],
            ['jQuery executed', typeof window.jQuery === 'function'],
            ['Summernote plugin registered', !!(window.jQuery && window.jQuery.fn && window.jQuery.fn.summernote)],
        ];

        document.getElementById('asset-check').innerHTML =
            '<strong>Option asset auto-loading</strong><br>' +
            checks.map(([label, ok]) => (ok ? '✅ ' : '❌ ') + label).join('<br>') +
            '<br><br>Open a block and hit the pencil — the sidebar should show a rich text editor.';
    });
</script>
</body>
