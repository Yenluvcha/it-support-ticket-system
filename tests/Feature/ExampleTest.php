<?php

test('guests are redirected to login from the dashboard', function () {
    $response = $this->get('/');

    $response->assertRedirect(route('login'));
});
