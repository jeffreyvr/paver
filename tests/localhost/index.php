<?php

use Jeffreyvr\Paver\Blocks\Block;
use Jeffreyvr\Paver\Blocks\Example;
use Jeffreyvr\Paver\Blocks\Options\Select;
use Jeffreyvr\Paver\Paver;

require __DIR__ . '/../../vendor/autoload.php';

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

$content = [];

if(isset($_GET['content'])) {
    $content[] = ['block' => 'paver.example'];
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
