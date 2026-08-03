<?php
require '/ven/autoload.php';
putenv('APP_KEY=base64:'.base64_encode(str_repeat('b',32)));
putenv('APP_ENV=testing'); putenv('APP_DEBUG=false');
putenv('DB_CONNECTION=sqlite'); putenv('DB_DATABASE=:memory:');
putenv('SESSION_DRIVER=array'); putenv('CACHE_STORE=array');
putenv('QUEUE_CONNECTION=sync'); putenv('MAIL_MAILER=array'); putenv('BCRYPT_ROUNDS=4');
function make_app(): \Illuminate\Foundation\Application {
    $app = require '/proj/bootstrap/app.php';
    $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    $app->register(\Laravel\Sanctum\SanctumServiceProvider::class);
    return $app;
}
