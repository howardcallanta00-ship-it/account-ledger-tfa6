<?php

use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

Artisan::command('ledger:check {--require-tls : Fail unless the database connection is encrypted}', function () {
    try {
        if (config('database.default') !== 'mysql') {
            $this->error('Expected a MySQL connection. Check DB_CONNECTION.');

            return 1;
        }

        foreach (['customers' => ['id', 'full_name', 'email', 'phone', 'created_at'], 'users' => ['id', 'username', 'full_name', 'created_at']] as $table => $columns) {
            if (Schema::getColumnListing($table) !== $columns) {
                $this->error('Account schema does not match. Check migration status.');

                return 1;
            }
        }

        // Server metadata only; account data is retrieved through Eloquent.
        $transport = Customer::getConnectionResolver()->connection()->selectOne("SHOW SESSION STATUS LIKE 'Ssl_cipher'");
        $encrypted = ! empty($transport->Value);
        if ($this->option('require-tls') && ! $encrypted) {
            $this->error('Database TLS is not active. Confirm the endpoint and trusted CA before deployment.');

            return 1;
        }

        $this->info('MySQL reachable; account columns match.');
        $this->line('Customers: '.Customer::count());
        $this->line('Users: '.User::count());
        $this->line('Database transport: '.($encrypted ? 'TLS active' : 'unencrypted (local development only)'));

        return 0;
    } catch (Throwable $exception) {
        $this->error('Database check failed. Verify connection settings, TLS and migrations. Credentials are not displayed.');

        return 1;
    }
})->purpose('Check account schema, record counts and database transport without exposing credentials');
