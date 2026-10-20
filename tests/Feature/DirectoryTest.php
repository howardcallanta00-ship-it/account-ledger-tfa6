<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DirectoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_pages_and_repeatable_seeding(): void
    {
        $this->seed();
        $this->seed();
        $this->assertDatabaseCount('customers', 5);
        $this->assertDatabaseCount('users', 5);
        foreach (['/', '/about', '/customers', '/users', '/up'] as $path) {
            $this->get($path)->assertOk();
        }
        $this->get('/customers')->assertSee('Camille Mendoza-Villanueva')->assertSee('Phone not provided');
        $this->assertFalse(Schema::hasColumn('users', 'updated_at'));
        $this->assertFalse(Schema::hasColumn('customers', 'updated_at'));
        $this->assertNotNull(Customer::first()->created_at);
        $this->assertNotNull(User::first()->created_at);
    }

    public function test_seeders_preserve_unrelated_records(): void
    {
        Customer::create(['full_name' => 'Unrelated Person', 'email' => 'unrelated@example.com']);
        $this->seed();
        $this->seed();
        $this->assertDatabaseCount('customers', 6);
        $this->assertDatabaseHas('customers', ['email' => 'unrelated@example.com']);
    }

    public function test_searches_all_fields_and_counts(): void
    {
        $this->seed();
        foreach (['Angela', 'angela.reyes@', '09171234567'] as $q) {
            $this->get('/customers?'.http_build_query(['q' => $q]))->assertOk()->assertViewHas('records', fn ($records) => $records->count() === 1)->assertSee('record matching');
        }
        foreach (['cashier01', 'Miguel'] as $q) {
            $this->get('/users?'.http_build_query(['q' => $q]))->assertOk()->assertViewHas('records', fn ($records) => $records->count() === 1);
        }
        $this->get('/customers?q=not-a-person')->assertSee('No matching records')->assertDontSee('No records available');
        $this->get('/customers?q='.urlencode("' OR 1=1 --"))->assertSee('No matching records');
    }

    public function test_empty_state_and_escaped_values(): void
    {
        $this->get('/customers')->assertSee('No records available')->assertDontSee('No matching records');
        User::create(['username' => '<script>alert(1)</script>', 'full_name' => 'Long Name']);
        $this->get('/users')->assertSee('&lt;script&gt;', false)->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_sorting_and_validation(): void
    {
        $this->seed();
        foreach (['customers' => ['id', 'full_name', 'email', 'phone'], 'users' => ['id', 'username', 'full_name']] as $kind => $columns) {
            foreach ($columns as $column) {
                foreach (['asc', 'desc'] as $direction) {
                    $model = $kind === 'customers' ? Customer::class : User::class;
                    $expected = $model::orderBy($column, $direction)->when($column !== 'id', fn ($q) => $q->orderBy('id'))->pluck('id')->all();
                    $this->get("/$kind?sort=$column&direction=$direction")->assertOk()->assertViewHas('records', fn ($records) => $records->pluck('id')->all() === $expected);
                }
            }
            $this->getJson("/$kind?sort=password")->assertUnprocessable();
            $this->getJson("/$kind?direction=invalid")->assertUnprocessable();
            $this->getJson("/$kind?sort[]=id")->assertUnprocessable();
            $this->getJson("/$kind?direction[]=asc")->assertUnprocessable();
            $this->getJson("/$kind?q[]=bad")->assertUnprocessable();
            $this->getJson("/$kind?q=".str_repeat('a', 101))->assertUnprocessable();
        }
        $this->get('/customers?q=Maria&sort=email&direction=desc')->assertSee('q=Maria', false)->assertSee('aria-sort="descending"', false);
    }

    public function test_username_unique_but_customer_email_not_unique(): void
    {
        Customer::create(['full_name' => 'One', 'email' => 'shared@example.com']);
        Customer::create(['full_name' => 'Two', 'email' => 'shared@example.com']);
        $this->assertDatabaseCount('customers', 2);
        User::create(['username' => 'same', 'full_name' => 'One']);
        $this->expectException(QueryException::class);
        User::create(['username' => 'same', 'full_name' => 'Two']);
    }

    public function test_database_failure_is_safe_and_recoverable(): void
    {
        Schema::drop('customers');
        $this->get('/customers')->assertStatus(503)->assertSee('Directory temporarily unavailable')->assertSee('Try again')->assertDontSee('secret query')->assertDontSee('secret password');
        $this->get('/customers?q=Maria&sort=email&direction=desc')
            ->assertStatus(503)->assertHeader('Retry-After', '30')
            ->assertSee('q=Maria', false)->assertSee('sort=email', false)->assertSee('direction=desc', false)
            ->assertDontSee('SQLSTATE')->assertDontSee('Stack trace');
    }

    public function test_name_ties_have_deterministic_id_order_and_models_can_update_without_updated_at(): void
    {
        $first = Customer::create(['full_name' => 'Equal Name', 'email' => 'first@example.com']);
        $second = Customer::create(['full_name' => 'Equal Name', 'email' => 'second@example.com']);
        $first->update(['phone' => '09170000000']);
        $this->assertSame('09170000000', $first->fresh()->phone);
        $this->get('/customers?sort=full_name&direction=desc')->assertOk()
            ->assertViewHas('records', fn ($records) => $records->pluck('id')->all() === [$first->id, $second->id]);
    }

    public function test_invalid_html_parameters_have_an_accessible_reset_path(): void
    {
        $this->get('/customers?q[]=invalid')->assertStatus(422)
            ->assertSee('Check directory options')->assertSee('Reset directory options')
            ->assertSee('role="alert"', false);
    }

    public function test_database_check_command_reports_real_counts(): void
    {
        if (config('database.default') !== 'mysql') {
            $this->markTestSkipped('Requires MySQL.');
        }
        $this->seed();
        $this->artisan('ledger:check')->expectsOutputToContain('Customers: 5')
            ->expectsOutputToContain('Users: 5')->assertSuccessful();
    }

    public function test_https_proxy_and_no_write_routes(): void
    {
        $this->withHeaders(['X-Forwarded-Proto' => 'https'])->get('/')->assertSee('https://localhost/assets/ledger.css', false);
        $this->post('/customers')->assertStatus(405);
        $this->get('/customers/create')->assertNotFound();
    }

    public function test_mysql_schema_matches_specification(): void
    {
        if (config('database.default') !== 'mysql') {
            $this->markTestSkipped('Requires MySQL; see README.');
        }
        foreach (['customers' => ['id' => ['int', false], 'full_name' => ['varchar(100)', false], 'email' => ['varchar(100)', false], 'phone' => ['varchar(20)', true], 'created_at' => ['datetime', false]], 'users' => ['id' => ['int', false], 'username' => ['varchar(50)', false], 'full_name' => ['varchar(100)', false], 'created_at' => ['datetime', false]]] as $table => $expected) {
            $columns = collect(Schema::getColumns($table))->keyBy('name');
            $this->assertSame(array_keys($expected), $columns->keys()->all());
            foreach ($expected as $name => [$type,$nullable]) {
                $this->assertStringContainsString($type, $columns[$name]['type']);
                $this->assertSame($nullable, $columns[$name]['nullable']);
            }
            $this->assertTrue($columns['id']['auto_increment']);
        }
    }
}
