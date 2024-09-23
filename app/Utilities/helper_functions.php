<?php

use App\Models\User;

function get_my_contracts_for_options()
{
    $user = get_auth_user();

    $contracts = $user->services_provider->contracts();

    return $contracts->get()->pluck('contract_number', 'id');
}


function get_auth_user(): User
{
    return auth()->user();
}
