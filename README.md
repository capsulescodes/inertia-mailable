
<p align="center"><img src="art/capsules-inertia-mailable-image.png" height="265px" alt="Inertia Mailable Image" /></p>

Seamlessly craft dynamic and reusable email templates using Inertia.

Inertia Mailable empowers you to build beautiful, component-driven emails in Laravel, utilizing the power of InertiaJS. Create interactive and responsive email designs effortlessly by composing Vue components and embedding them into your mailables.

<br>

 [This article](https://capsules.codes/en/blog/fyi/en-fyi-build-emails-with-inertia-mailable) provides an in-depth exploration of the package.

<br>

> [!NOTE]
> This package is currently designed for Laravel and Vue users and is still in development.

<br>

## Installation

**1. Install package and publish expected inertia mailable file**

```bash
composer require capsulescodes/inertia-mailable

php artisan vendor:publish --tag=inertia-mailable-vue-js
```

<br>

It publishes two files :

 - `resources/js/mail.js` : base Inertia file
 - `resources/js/mails/Welcome.vue` : example Vue Component.

<br>

**2. Add filename into vite config's input array**

```javascript
plugins : [
    laravel( {
        input : [ ..., 'resources/js/mail.js' ],
    } )
```

<br>

**3. Build files**

```
npm run build
```

<br>

## Usage

```
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

```
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
- [x] Inertia Mailable supports Vue with Tailwindcss.

<br>

## Options

**1. Compile files in `bootstrap/ssr` directory**

If you want the compiled files to be stored in the `bootstrap/ssr` directory :

```diff
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';


export default defineConfig( {
-    plugins : [ laravel( { input : [ 'resources/css/app.css', 'resources/js/app.js', 'resources/js/mail.js' ], ssr : 'resources/js/ssr.js' } ), vue() ],
+    plugins : [ laravel( [] ), vue() ],
    resolve : { alias : { '~': '/resources/js' } },
+    build : {
+        manifest : 'public/build/manifest.json',
+        rollupOptions : {
+            input : [ 'resources/css/app.css', 'resources/js/app.js', 'resources/js/ssr.js', 'resources/js/mail.js' ],
+            output : {
+                assetFileNames : () => 'public/build/assets/[name]-[hash][extname]',
+                entryFileNames : chunkInfo => chunkInfo.name.includes( 'ssr' ) || chunkInfo.name.includes( 'mail' ) ? 'bootstrap/ssr/[name].js' : 'public/build/[name].js',
+                chunkFileNames : () => 'public/build/assets/[name]-[hash].js',
+                dir : './'
+            }
+        }
+    }
} );
```

<br>

`config.inertia-mailable.php`
```
<?php

return [

    'build' => base_path(),

];
```

<br>

**2. Add a custom CSS file or Blade file**

If you want to modify the current css and blade files, publish the templates and modify the paths in the config file

```
php artisan vendor:publish --tag=inertia-mailable-config

php artisan vendor:publish --tag=inertia-mailable-css

php artisan vendor:publish --tag=inertia-mailable-blade
```

<br>

## Contributing

Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.
In order to run MySQL tests, credentials have to be configured in the intended TestCases.

<br>

## Testing

```
composer test
```

<br>

## Credits

- [Capsules Codes](https://github.com/capsulescodes)

## License

[MIT](https://choosealicense.com/licenses/mit/)
