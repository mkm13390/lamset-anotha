
<?php

test('the API status endpoint returns the expected payload', function () {
    $response = $this->getJson('/api/v1/status');

    $response
        ->assertOk()
        ->assertExactJson([
            'success' => true,
            'service' => 'lamset-anotha-api',
            'version' => 'v1',
        ]);
});
