<?php

test('the root redirects to the default English locale', function () {
    $this->get('/')->assertRedirect('/en');
});

test('the public home page renders in English', function () {
    $this->get('/en')->assertOk()->assertSee('ENCAP');
});

test('the public home page renders in Arabic with RTL document direction', function () {
    $this->get('/ar')->assertOk()->assertSee('dir="rtl"', false);
});

test('the sitemap is served as XML', function () {
    $this->get('/sitemap.xml')->assertOk()->assertHeader('content-type', 'application/xml; charset=UTF-8');
});

test('quote requests validate required contact fields', function () {
    $this->post('/en/request-a-quote', [])->assertSessionHasErrors(['contact_name', 'email', 'country', 'message']);
});

test('the public content and enquiry pages render', function () {
    foreach ([
        '/en/about',
        '/en/farms',
        '/en/products',
        '/en/quality',
        '/en/export',
        '/en/gallery',
        '/en/contact',
        '/en/request-a-quote',
    ] as $path) {
        $this->get($path)->assertOk();
    }
});

test('contact messages validate required fields', function () {
    $this->post('/en/contact', [])
        ->assertSessionHasErrors(['name', 'email', 'subject', 'message']);
});

