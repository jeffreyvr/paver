<?php

/*
 * Test page for the paver:option-init lifecycle event.
 *
 * The option below contains no Alpine code at all: it declares its assets,
 * renders a container, and a plain JavaScript listener turns that container
 * into a Summernote editor.
 *
 * Run: npm run start-server, then open http://localhost:8000/lifecycle.php
 */

use Jeffreyvr\Paver\Blocks\Block;
use Jeffreyvr\Paver\Blocks\Options\Input;
use Jeffreyvr\Paver\Blocks\Options\Option;
use Jeffreyvr\Paver\Paver;

require __DIR__ . '/../../vendor/autoload.php';

class RichText extends Option
{
    public string $type = 'richtext';

    public function __construct(
        public string $label,
        public string $name
    ) {
        $this->script('jquery', 'https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js');
        $this->script('summernote', 'https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js', ['jquery']);
        $this->style('summernote', 'https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css');
    }

    public function render(): string
    {
        return $this->container('<textarea></textarea>');
    }
}

class Article extends Block
{
    public string $name = 'Article';

    public static string $reference = 'lifecycle_demo.article';

    public array $data = [
        'title' => 'A title',
        'content' => '',
    ];

    public function options()
    {
        return [
            Input::make('Title', 'title'),
            RichText::make('Content', 'content'),
        ];
    }

    public function render()
    {
        $content = $this->data['content'] ?? '';

        if (trim($content) === '' && $this->isInEditor()) {
            $content = '<p style="color: #9ca3af;">Empty — edit me…</p>';
        }

        return '<div style="padding: 15px;"><h2>'.$this->data['title'].'</h2>'.$content.'</div>';
    }
}

$paver = Paver::instance();

$paver->api->setEndpoint('lifecycle.php?api');

$paver->registerBlock(Article::class);

$content = [
    ['block' => 'lifecycle_demo.article', 'data' => [
        'title' => 'Hello',
        'content' => '<p>Edit me in the sidebar.</p>',
    ]],
];

if (isset($_GET['api'])) {
    Jeffreyvr\Paver\Endpoints\Handler::run();
}
?>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<body class="flex h-screen">

<?php echo $paver->render($content); ?>

<div id="check" style="position:fixed;bottom:12px;left:12px;z-index:9999;background:#111;color:#eee;font:12px/1.6 ui-monospace,monospace;padding:12px 16px;border-radius:8px;max-width:380px">
    Open the block and hit the pencil…
</div>

<script>
    const log = [];

    // This is the entire integration: no Alpine, no knowledge of how the
    // sidebar is rendered.
    document.addEventListener('paver:option-init', (event) => {
        const { el, type, name, value, setValue } = event.detail;

        if (type !== 'richtext') return;

        log.push(`event fired for "${name}", initial value: ${JSON.stringify(value)}`);

        const textarea = el.querySelector('textarea');

        jQuery(textarea).summernote({
            height: 200,
            dialogsInBody: true,
            callbacks: {
                onChange: (contents) => setValue(contents),
            },
        });

        jQuery(textarea).summernote('code', value || '');

        log.push('summernote initialized, value written back via setValue()');
        render();
    });

    function render() {
        document.getElementById('check').innerHTML =
            '<strong>paver:option-init</strong><br>' +
            log.map(l => '✅ ' + l).join('<br>') +
            '<br><br>Type in the editor — the canvas should update live.';
    }
</script>
</body>
