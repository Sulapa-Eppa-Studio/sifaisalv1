<?php

use App\Models\Contract;
use App\Models\PaymentRequest;
use App\Models\TermintSppPpk;
use App\Models\User;

function get_my_contracts_for_options()
{
    $user = get_auth_user();

    $contracts = $user->services_provider->contracts();

    return $contracts->get()->pluck('contract_number', 'contract_number');
}

function get_auth_user(): User
{
    return auth()->user();
}

function get_list_ppk_request($status = null)
{
    return TermintSppPpk::get()->pluck('no_termint', 'id')->toArray();
}

function get_list_request_payment($status = null)
{
    $query = PaymentRequest::query();

    if ($status) {
        $query->where('verification_progress', $status);
    }

    return $query->get()->pluck('request_number', 'id')->toArray();
}


function cek_pembayaran_pertama(Contract $contract) : bool
{
    $payment_requests = PaymentRequest::where('contract_number', $contract->contract_number)
        ->where('verification_progress', 'done')
        ->count();

    // if ($contract->advance_payment == true) {
    //     return $payment_requests == 0;
    // }

    // $contract->advance_payment == true ? false : true;

    return $payment_requests == 0 && $contract->advance_payment == true;
}


function get_payment_stage(Contract $contract) : int
{
    $payment_requests = PaymentRequest::where('contract_number', $contract->contract_number)
        ->where('verification_progress', 'done')
        ->count();

    return $payment_requests + 1;
}
