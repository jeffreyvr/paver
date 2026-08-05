<?php

use Jeffreyvr\Paver\Blocks\Block;
use Jeffreyvr\Paver\Blocks\Example;
use Jeffreyvr\Paver\Blocks\Options\Checkbox;
use Jeffreyvr\Paver\Blocks\Options\Color;
use Jeffreyvr\Paver\Blocks\Options\Input;
use Jeffreyvr\Paver\Blocks\Options\Link;
use Jeffreyvr\Paver\Blocks\Options\Number;
use Jeffreyvr\Paver\Blocks\Options\RadioGroup;
use Jeffreyvr\Paver\Blocks\Options\Range;
use Jeffreyvr\Paver\Blocks\Options\Select;
use Jeffreyvr\Paver\Blocks\Options\Textarea;
use Jeffreyvr\Paver\Paver;

require __DIR__ . '/../../vendor/autoload.php';

/**
 * Every option the sidebar can render, on one block.
 *
 * This is the harness for the sidebar's field styling: it puts each input
 * type, the empty and disabled states, an option that only appears once
 * another is set, and a label too long for the 300px rail side by side, so
 * the whole surface can be judged at once instead of one field at a time.
 */
class AllOptions extends Block
{
    public string $name = 'All options';

    public static string $reference = 'paver.all-options';

    public array $data = [
        'heading' => 'Every option type',
        'summary' => 'Change anything in the sidebar and it lands here.',
        'layout' => 'stacked',
        'postType' => 'post',
        'quantity' => '3',
        'email' => '',
        'publishedOn' => '',
        'accent' => '#0891b2',
        'scale' => '100',
        // 'featured' and 'cta' are left out on purpose: Checkbox and Link
        // seed them from their own defaults.
        'badge' => "Editor's pick",
        'reference' => 'paver.all-options',
    ];

    public function options()
    {
        return [
            Input::make('Heading', 'heading'),
            Textarea::make('Summary', 'summary', ['rows' => '3']),

            // Same shape, two controls: the segmented group shows both
            // choices, the select hides them until opened.
            RadioGroup::make('Layout', 'layout', [
                'stacked' => 'Stacked',
                'inline' => 'Inline',
            ]),
            Select::make('Post type', 'postType', [
                'post' => 'Posts',
                'page' => 'Pages',
                'product' => 'Products',
            ]),

            Number::make('Quantity', 'quantity', min: 1, max: 12),
            Range::make('Scale', 'scale', min: 50, max: 150, step: 5, suffix: '%'),
            Color::make('Accent color', 'accent'),
            Link::make('Call to action', 'cta'),

            // Types the old `input[type=text], input[type=number]` selector
            // missed entirely, which is half the reason this block exists.
            Input::make('Email address', 'email', ['type' => 'email', 'placeholder' => 'name@example.com']),
            Input::make('Published on', 'publishedOn', ['type' => 'date']),
            Checkbox::make('Featured', 'featured'),

            // Only rendered while Featured is ticked.
            Input::make('Badge text', 'badge')->condition('featured'),

            // A label with nowhere to wrap, and a field nobody can type in.
            Input::make('Block reference used at registration', 'reference', ['disabled' => 'disabled']),
        ];
    }

    public function render()
    {
        $escape = fn ($value) => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

        $accent = $escape($this->data['accent'] ?: '#0891b2');
        $inline = ($this->data['layout'] ?? 'stacked') === 'inline';

        $badge = ! empty($this->data['featured'])
            ? '<span class="rounded-full px-2 py-0.5 text-xs font-medium text-white" style="background-color: '.$accent.'">'
                .$escape($this->data['badge']).'</span>'
            : '';

        $fields = [
            'Layout' => $this->data['layout'],
            'Post type' => $this->data['postType'],
            'Quantity' => $this->data['quantity'],
            'Email address' => $this->data['email'],
            'Published on' => $this->data['publishedOn'],
            'Scale' => $this->data['scale'] !== '' ? $this->data['scale'].'%' : '',
        ];

        $rows = '';

        foreach ($fields as $label => $value) {
            $value = trim((string) $value) === ''
                ? '<span class="text-gray-400 italic">not set</span>'
                : $escape($value);

            $rows .= '<div class="flex justify-between gap-4 border-b border-gray-100 py-1.5">'
                .'<dt class="text-gray-500">'.$escape($label).'</dt>'
                .'<dd class="font-medium">'.$value.'</dd>'
                .'</div>';
        }

        $listClass = $inline ? 'grid grid-cols-2 gap-x-8' : '';

        $link = $this->data['cta'] ?? [];
        $cta = '';

        if (trim((string) ($link['url'] ?? '')) !== '') {
            $target = ! empty($link['newTab']) ? ' target="_blank" rel="noopener"' : '';
            $text = trim((string) ($link['text'] ?? '')) !== '' ? $link['text'] : $link['url'];

            $cta = '<a href="'.$escape($link['url']).'"'.$target
                .' class="mt-4 inline-block rounded px-3 py-1.5 text-sm font-medium text-white"'
                .' style="background-color: '.$accent.'">'.$escape($text).'</a>';
        }

        // Interpolation cannot call a closure, so escape ahead of the heredoc.
        $heading = $escape($this->data['heading']);
        $summary = $escape($this->data['summary']);

        return <<<HTML
            <div class="p-6" style="border-top: 3px solid {$accent}">
                <div class="flex items-center gap-3">
                    <h2 class="text-xl font-semibold">{$heading}</h2>
                    {$badge}
                </div>
                <p class="mt-1 text-gray-600">{$summary}</p>
                <dl class="mt-4 text-sm {$listClass}">{$rows}</dl>
                {$cta}
            </div>
        HTML;
    }
}

/**
 * A two column layout, for testing nested blocks.
 *
 * Children are one flat list per block, so the columns are child blocks
 * rather than two zones on this one: Columns holds Column blocks, and each
 * Column holds whatever you drop into it.
 */
class Columns extends Block
{
    public string $name = 'Two columns';

    public static string $reference = 'paver.columns';

    // A freshly inserted block starts with two empty columns.
    public array $children = [
        ['block' => 'paver.column'],
        ['block' => 'paver.column'],
    ];

    public array $data = [
        'gap' => '4',
    ];

    public function options()
    {
        return [
            Select::make('Gap', 'gap', [
                '0' => 'None',
                '4' => 'Normal',
                '8' => 'Wide',
            ]),
        ];
    }

    public function render()
    {
        $gap = (int) ($this->data['gap'] ?? 4);

        return <<<HTML
            <!-- Paver::children({"allowBlocks": ["paver.column"], "attributes": {"class": "grid grid-cols-2 gap-{$gap} p-4", "data-direction": "horizontal"}}) -->
        HTML;
    }
}

class Column extends Block
{
    public string $name = 'Column';

    public static string $reference = 'paver.column';

    // Only offered inside blocks that allow it.
    public bool $asChildOnly = true;

    public function render()
    {
        $outline = $this->isInEditor() ? 'outline outline-1 outline-dashed outline-gray-300' : '';

        return <<<HTML
            <!-- Paver::children({"attributes": {"class": "min-h-20 {$outline}"}}) -->
        HTML;
    }
}

$paver = Paver::instance();

$paver->api->setEndpoints([
    'options' => 'index.php?options',
    'render' => 'index.php?render',
    'fetch' => 'index.php?fetch',
]);

$paver->frame->headHtml = <<<HTML
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
HTML;

$paver->registerBlock(Example::class);
$paver->registerBlock(Columns::class);
$paver->registerBlock(Column::class);
$paver->registerBlock(AllOptions::class);

$content = [];

if(isset($_GET['content'])) {
    $content[] = ['block' => 'paver.example'];
}

// index.php?options-demo drops the block straight onto the canvas, so the
// sidebar can be opened on it without inserting it by hand first.
if(isset($_GET['options-demo'])) {
    $content[] = ['block' => 'paver.all-options'];
}

if(isset($_GET['columns'])) {
    $content[] = ['block' => 'paver.columns', 'data' => ['gap' => '4'], 'children' => [
        ['block' => 'paver.column', 'children' => [
            ['block' => 'paver.example', 'data' => ['name' => 'left']],
        ]],
        ['block' => 'paver.column', 'children' => [
            ['block' => 'paver.example', 'data' => ['name' => 'right']],
        ]],
    ]];
}

require 'endpoints.php';
?>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<body class="flex h-screen">
<script src="https://cdn.tailwindcss.com?plugins=forms"></script>

<?php
$paver->debug(true);
?>

<?php echo $paver->render($content, ['config' => [
    'showExpandButton' => false,
    'showViewButton' => true,
]]); ?>
</body>
