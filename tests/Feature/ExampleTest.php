<?php

test('redirects visitors to login', function () {
    $response = $this->get(route('home'));

    $response->assertRedirect('/login');
});
