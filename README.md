# Htsl-laravel
Laravel service provider of Htsl.php

## Usage

I. Install,
``` bash
composer require htsl/htsl:@dev htsl/for-laravel:@dev
```

II. Then add the provider to your autoloaded service provider list in your config/app.php.
``` php
Htsl\ForLaravel\HtslServiceProvider::class,
```

III. Publish the vendor.
``` bash
./artisan vendor:publish
```

IV. Create views with extension .htsl, and enjoy the HTSL!

more:
The vender:publish command create two files:
  a. config/htsl.php
  b. app/Htsl/TExtension.php

With config/htsl.php editing, you can change the way Htsl.php works, even add new feature into.
With adding method into app/Htsl/TExtension.php, you can add the method to the $this in the views.
