<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Blade;
use Pest\Expectation;

beforeEach(function () {
    Artisan::call('view:clear');
});

function expectBlade(string $blade, array $data = []): Expectation
{
    $blade = Blade::render($blade, $data, deleteCachedView: true);

    return expect($blade);
}

it('can capture a block of code', function () {
    expectBlade(<<<blade
        @capture(\$hello, \$name)
            Hello {{ \$name }}!
        @endcapture

        {{ \$hello('Ryan') }}
        {{ \$hello('Dan') }}
    blade)
        ->toContain('Hello Ryan!')
        ->toContain('Hello Dan!');
});

it('can capture a block of code with zero arguments', function () {
    expectBlade(<<<blade
        @capture(\$hello)
            Hello!
        @endcapture

        {{ \$hello() }}
    blade)
        ->toContain('Hello!');
});

it('can capture a block of code with a trailing comma', function () {
    expectBlade(<<<blade
        @capture(\$hello,)
            Hello!
        @endcapture

        {{ \$hello() }}
    blade)
        ->toContain('Hello!');

    expectBlade(<<<blade
        @capture(\$hello, \$name,)
            Hello {{ \$name }}!
        @endcapture

        {{ \$hello('Ryan') }}
    blade)
        ->toContain('Hello Ryan!');
});

it('supports default arguments', function () {
    expectBlade(<<<blade
        @capture(\$hello, \$name, \$greeting = 'Hello')
            {{ \$greeting }} {{ \$name }}!
        @endcapture

        {{ \$hello('Ryan') }}
        {{ \$hello('Dan', 'Yo') }}
    blade)
        ->toContain('Hello Ryan!')
        ->toContain('Yo Dan!');
});

it('captures the external environment', function () {
    expectBlade(<<<blade
        @php(\$name = 'Ryan')

        @capture(\$hello)
            Hello, {{ \$name }}!
        @endcapture

        {{ \$hello() }}
    blade)
        ->toContain('Hello, Ryan!');
});
