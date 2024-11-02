<?php 

namespace App\OAuth\Provider;

use League\OAuth2\Client\Provider\Google;

class GoogleProvider
{
    public function __invoke(array $options)
    {
        return new Google($options);
    }
}