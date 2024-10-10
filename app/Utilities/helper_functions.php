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

function get_my_contracts_for_options_by_ppk()
{
    $user = get_auth_user();

    $contracts = $user->ppk->contracts();


    return $contracts->get()->pluck('contract_number', 'contract_number');
}

function get_auth_user(): User
{
    return auth()->user();
}

function get_list_ppk_request($status = null)
{
    return TermintSppPpk::where('ppspm_verification_status', 'approved')->get()->pluck('no_termint', 'id')->toArray();
}

function get_list_request_payment($status = null)
{
    $query = PaymentRequest::query();

    if ($status) {
        $query->where('verification_progress', $status);
    }

    return $query->get()->pluck('request_number', 'id')->toArray();
}


function cek_pembayaran_pertama(Contract $contract): bool
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


function get_payment_stage(Contract $contract): int
{
    $payment_requests = PaymentRequest::where('contract_number', $contract->contract_number)
        ->where('verification_progress', 'done')
        ->count();

    return $payment_requests + 1;
}
function get_admin_panel_url()
{
    return env('DOMAIN_ADMIN') ?? env('APP_URL');
}

function get_sp_panel_url()
{
    return env('DOMAIN_SP') ?? env('APP_URL');
}

function get_ppk_panel_url()
{
    return env('DOMAIN_PPK') ?? env('APP_URL');
}

function get_spm_panel_url()
{
    return env('DOMAIN_SPM') ?? env('APP_URL');
}

function get_treasurer_panel_url()
{
    return env('DOMAIN_TREASURER') ?? env('APP_URL');
}

function get_kpa_panel_url()
{
    return env('DOMAIN_KPA') ?? env('APP_URL');
}
