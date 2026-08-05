# Security

- [Escaping block output](#escaping-block-output)
- [The resolver runs your code](#the-resolver-runs-your-code)
- [Endpoints are yours to protect](#endpoints-are-yours-to-protect)
- [Stored content is untrusted input](#stored-content-is-untrusted-input)

Paver renders whatever your blocks return, and calls whatever your options
expose. It does not sanitize on your behalf. The three places that matter are
your `render` methods, your resolver methods, and your endpoints.

## Escaping block output

A block's `render` method returns raw HTML, which is the point — it is how you
control your own markup. It also means every value you interpolate is
unescaped unless you escape it yourself.

Option values come from whoever is using the editor. Treat them the way you
would treat any other user input.

```php
// Bad: the value lands in the document as markup.
public function render()
{
    return "<h2>{$this->data['title']}</h2>";
}
```

```php
// Good.
public function render()
{
    $title = htmlspecialchars($this->data['title'], ENT_QUOTES, 'UTF-8');

    return "<h2>{$title}</h2>";
}
```

Attributes need the same treatment, and a URL needs more than escaping — a
`javascript:` URL survives `htmlspecialchars` intact:

```php
$url = $this->data['cta']['url'];

if (! in_array(parse_url($url, PHP_URL_SCHEME), ['http', 'https'], true)) {
    $url = '';
}

$url = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
```

If a block is meant to store rich text, you are deliberately storing HTML. Run
it through an HTML sanitizer with an allow list before rendering it, and decide
who is allowed to use that block. In WordPress, `wp_kses_post` is a reasonable
default.

## The resolver runs your code

The [resolver](/docs/{{version}}/custom-options#calling-your-options-php-from-the-browser)
lets an option call its own public methods from the browser. Paver checks two
things: the class extends `Option` or `Block`, and the method is public and not
static. It does not check anything else.

So a public method on your option class is a public endpoint. Assume the
browser controls the method name, the arguments, and the properties set on the
instance beforehand.

```php
// Bad: the caller chooses the path.
public function readTemplate(string $path): string
{
    return file_get_contents($path);
}
```

```php
// Good: the caller chooses from what you allow.
public function readTemplate(string $key): string
{
    $templates = ['hero' => 'hero.html', 'footer' => 'footer.html'];

    if (! isset($templates[$key])) {
        throw new \InvalidArgumentException('Unknown template');
    }

    return file_get_contents(__DIR__.'/templates/'.$templates[$key]);
}
```

Practical rules:

- Validate and type every argument. They arrive as JSON from the browser.
- Never pass an argument into a file path, a shell command, or raw SQL.
- Check permissions inside the method. The resolver does not know who is
  calling, and being in the editor is not a permission.
- Keep the surface small. Make helper methods `protected` or `private` so they
  are not reachable — only the ones you deliberately expose should be public.
- Do not return anything the caller should not see. The return value goes
  straight to the browser as JSON.

## Endpoints are yours to protect

The `options`, `render`, `fetch` and `resolve` endpoints are wired up by you,
in your application, and they inherit whatever protection you give that route.
Paver adds none of its own.

At minimum, put them behind the same authentication and authorization as the
rest of your admin, and apply your framework's CSRF protection. In Laravel that
means a route inside your authenticated middleware group; in WordPress it means
a capability check and a nonce.

Anyone who can reach the `render` endpoint can render any registered block with
data they control.

## Stored content is untrusted input

Paver stores block content as JSON in a field you control. Whatever comes back
out of your database goes through `render` again, so it needs the same escaping
on the way out that it needed on the way in — content saved by a user who was
trusted at the time is still untrusted input today.

Escaping at render time, rather than sanitizing once at save time, means
content saved before you fixed a block gets the fix too.
