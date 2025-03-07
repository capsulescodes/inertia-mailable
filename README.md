
<p align="center"><img src="art/capsules-inertia-mailable-image.png" height="265px" alt="Inertia Mailable Image" /></p>

Seamlessly craft dynamic and reusable email templates using Inertia.

Inertia Mailable empowers you to build beautiful, component-driven emails in Laravel, utilizing the power of InertiaJS. Create interactive and responsive email designs effortlessly by composing components and embedding them into your mailables.

<br>

For React users, [ this article ](https://capsules.codes/en/blog/fyi/en-fyi-craft-emails-with-react-and-tailwind-using-inertia-mailable) provides an in-depth exploration of the package.
For Vue users, [ this article ](https://capsules.codes/en/blog/fyi/en-fyi-craft-emails-with-vue-and-tailwind-using-inertia-mailable) provides an in-depth exploration of the package.



<br>

## Installation

**1. Install package and publish expected inertia mailable file**

```bash
composer require capsulescodes/inertia-mailable

> php artisan vendor:publish
```

```bash
┌ Which provider or tag's files would you like to publish? ───────────────┐
 │ Search...                                                               │
 ├─────────────────────────────────────────────────────────────────────────┤
 │   ...                                                                 │ │
 │   Tag: inertia-mailable-react-js                                      │ │
 │   Tag: inertia-mailable-react-ts                                      │ │
 │   Tag: inertia-mailable-vue-js                                        │ │
 │   Tag: inertia-mailable-vue-ts                                        │ │
 │   ...                                                                 │ │
```

<br>

It publishes three files :

 - `resources/css/mail.css` : base Tailwind CSS file
 - `resources/{js,ts}/mail.{js,ts,jsx,tsx}` : base Inertia file
 - `resources/{js,ts}/mails/Welcome.{jsx,tsx,vue}` : example Components

<br>

**2. Add Inertia file and CSS file in Laravel vite config ssr array**

`vite.config.js`
```javascript
plugins : [
    laravel( {
        input : [ ..., 'resources/css/mail.css' ],
        ssr : [ ..., 'resources/{js,ts}/mail.{js,ts,jsx,tsx}' ],
    } )
```

<br>

**3. Add SSR to `build` script and build files**

`package.json`
```json
"scripts" : {
    "build" : "vite build && vite build --ssr"
},
```

```bash
npm run build
```

<br>

## Usage

```bash
php artisan make:mail InertiaMailableInstalled.php
```

<br>

`App\Mails\InertiaMailableInstalled.php`

```diff
<?php

namespace App\Mail;

- use Illuminate\Mail\Mailable;
+ use CapsulesCodes\InertiaMailable\Mail\Mailable;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
- use Illuminate\Mail\Mailables\Content;
+ use CapsulesCodes\InertiaMailable\Mail\Mailables\Content;


class InertiaMailableInstalled extends Mailable
{
    private string $name;


    public function __construct( string $name )
    {
        $this->name = $name;
    }


    public function envelope() : Envelope
    {
        return new Envelope( from : new Address( 'example@example.com', 'Mailable World' ), subject : 'Hello Inertia Mailable World!' );
    }

    public function content() : Content
    {
-       return new Content( view: 'view.name' );
+       return new Content( view : 'Welcome', props : [ 'name' => $this->name ] );
    }

    public function attachments() : array
    {
        return [];
    }
}
```

<br>

`routes/web.php`

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Mail\InertiaMailableInstalled;


Route::get( '/render', fn() => ( new InertiaMailableInstalled( "Mailable World" ) )->render() );
```

<br>

```bash
php artisan serve


INFO  Server running on [http://127.0.0.1:8000].
```

<br>

`> http://127.0.0.1:8000/render`

<p align="center"><img src="art/capsules-inertia-mailable-screenshot.png" alt="Inertia Mailable Screenshot" /></p>

<br>

You are now ready to send.

<br>

`routes/web.php`

```php
<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Mail\InertiaMailableInstalled;


Route::get( '/send', function(){ Mail::to( 'example@example.com' )->send( new InertiaMailableInstalled( "Mailable World" ) ); } );
```
- replace 'example@example.com' with the desired email address in `routes/web.php`and `App\Mail\InertiaMailableInstalled.php`.

<br>
<br>

## Supported Frameworks

- [x] Inertia mailable supports Laravel.

<br>

- [x] Inertia Mailable supports Vue.
- [x] Inertia Mailable supports Vue with Typescript.
- [x] Inertia Mailable supports Vue with Tailwind CSS.

<br>

- [x] Inertia Mailable supports React.
- [x] Inertia Mailable supports React with Typescript.
- [x] Inertia Mailable supports React with Tailwind CSS.

<br>

## Options

**- Build your email with Watch mode**

You can dynamically build your component while working on it by enabling the `--watch` option in your `package.json` script. This ensures your components are rebuilt automatically when changes are detected.

```json
"scripts" : {
    "watch" : "vite build --ssr --watch"
},
```

```bash
> npm run watch

watching for file changes...
```

<br>

**- Add a custom root blade view**

If you want to modify the current blade file, publish the template and modify the path in the `inertia-mailable` config file.

```bash
php artisan vendor:publish --tag=inertia-mailable-blade
```

<br>

`App\Mails\InertiaMailableInstalled.php`

```php
...

public function content() : Content
{
    return new Content( root : 'custom-blade-view', view : 'Welcome', props : [ 'name' => $this->name ] );
}

...
```

<br>
<br>

**- Specify the actual path to node**

If you encounter the following error : `sh: line 0: exec: node: not found`, add node binary's absolute path in the `inertia-mailable` config file or add `NODE_PATH` in your `.env` file.

<br>

`config/inertia-mailable.php`

```php

return [

    ...

    'node' => env( 'NODE_PATH', 'node' ),

    ...
];

```

<br>
<br>

**- Emit CSS file in SSR directory**

Since Vite, by default, does not emit assets outside the `public` directory, Inertia Mailable follows the same approach. However, if you want to build all related files into the `ssr` directory, indicate it in the Vite config file and change the Inertia mailable config file.

<br>

`vite.config.js`
```javascript
plugins : [
    laravel( {
        ssr : [ ..., 'resources/css/mail.css', 'resources/{js,ts}/mail.{js,ts,jsx,tsx}' ],
    } ),
    ...
],
build : {
    manifest : 'manifest.json',
    ssrEmitAssets : true,
}
```

<br>

`config/inertia-mailable.php`
```php

return [

    ...

    'inertia' => 'resources/{js,ts}/mail.{js,ts,jsx,tsx}',
    'manifest' => 'bootstrap/ssr/manifest.json'

    ...
];
```

<br>

## Contributing

Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.
Please make sure to update tests as appropriate.

<br>

## Testing

```
composer test
```

<br>

## Credits

[Capsules Codes](https://github.com/capsulescodes)

<br>

## License

[MIT](https://choosealicense.com/licenses/mit/)
