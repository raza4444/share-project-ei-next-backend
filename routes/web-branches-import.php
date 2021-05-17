<?php

use App\Http\RouteUtils;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

Route::group([
    'prefix' => 'companies'
], function () {

    Route::get('test', 'Publics\Branches\PublicCompanyController@test');

    /**
     * Fuegt ein neues Unternehmen in I ein
     */
    Route::post('companies', 'Publics\Branches\PublicCompanyController@importSingle');

    /**
     * Ueberfuehrt ein Unternehmen aus I in die tatsaechliche Unternehmensstruktur (companies)
     */
    Route::post('companies/{id}/use', 'Publics\Branches\PublicCompanyController@createRealSingleCompany');

    /**
     * Ueberfuehrt alle nicht ueberfuehrten Unternehmen aus I in die tatsaechliche Struktur
     */
    Route::post('companies/use-all', 'Publics\Branches\PublicCompanyController@createRealCompanies');

    /**
     * Deaktiviert aktuelle Ereignisse mittels eine Menge an Telefonnummern
     */
    Route::post('events/deactivate-by-phone-numbers', 'Publics\Branches\PublicEventDeactivationController@deactivateByPhoneNumbers');

});
