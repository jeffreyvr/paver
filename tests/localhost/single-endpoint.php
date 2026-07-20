<?php

/*
 * Test page for the consolidated endpoint.
 *
 * One route serves every action. Compare with index.php, which uses the
 * older per action setup — both are exercised by the checks at the bottom.
 *
 * Run: npm run start-server, then open http://localhost:8000/single-endpoint.php
 */

use Jeffreyvr\Paver\Blocks\Example;
use Jeffreyvr\Paver\Endpoints\Handler;
use Jeffreyvr\Paver\Paver;

require __DIR__ . '/../../vendor/autoload.php';

$paver = Paver::instance();

// One endpoint, instead of four.
$paver->api->setEndpoint('single-endpoint.php?api');

$paver->registerBlock(Example::class);

$content = [['block' => 'paver.example']];

if (isset($_GET['api'])) {
    Handler::run();
}
?>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<body class="flex h-screen">

<?php echo $paver->render($content); ?>

<div id="check" style="position:fixed;bottom:12px;left:12px;z-index:9999;background:#111;color:#eee;font:12px/1.6 ui-monospace,monospace;padding:12px 16px;border-radius:8px;max-width:420px">
    checking…
</div>

<script>
    window.addEventListener('load', async () => {
        const endpoint = 'single-endpoint.php?api';

        const call = async (body) => {
            const r = await fetch(endpoint, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(body),
            });
            return { status: r.status, body: await r.text() };
        };

        // fetch takes a block reference string, the others take a block object
        const fetchRes = await call({action: 'fetch', block: 'paver.example'});
        const optionsRes = await call({action: 'options', block: {block: 'paver.example', data: {}}});
        const renderRes = await call({action: 'render', block: {block: 'paver.example', data: {}}});
        const unknownRes = await call({action: 'nope'});
        const missingRes = await call({});

        const checks = [
            ['one endpoint configured', !!document.body.innerHTML.includes('single-endpoint.php?api')],
            ['action: fetch → 200', fetchRes.status === 200 && fetchRes.body.includes('render')],
            ['action: options → 200', optionsRes.status === 200 && optionsRes.body.includes('optionsHtml')],
            ['action: render → 200', renderRes.status === 200],
            ['unknown action → 500 with helpful error', unknownRes.status === 500 && unknownRes.body.includes('Unknown action')],
            ['missing action → 500 with helpful error', missingRes.status === 500 && missingRes.body.includes('No action given')],
        ];

        document.getElementById('check').innerHTML =
            '<strong>Single endpoint</strong><br>' +
            checks.map(([l, ok]) => (ok ? '✅ ' : '❌ ') + l).join('<br>') +
            '<br><br>Drag in a block and edit it — all traffic goes to one URL.';
    });
</script>
</body>
