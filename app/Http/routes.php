<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::post	 ('/Account'							, 'testCon@addAccount' 		);//add new account
Route::get	 ('/Account'							, 'testCon@getAllAccount' 	);//get all account
Route::get	 ('/Account/{id}'						, 'testCon@getAccount' 		);//get selection account
Route::put	 ('/Account/{id}'						, 'testCon@editAccount'		);//edit selection account
Route::delete('/Account'							, 'testCon@removeAllAccount');//delete all account
Route::delete('/Account/{id}'						, 'testCon@removeAccount' 	);//delete selection account
Route::post	 ('/Transaction/{id}/withdraw'			, 'testCon@addWithdraw' 	);//insert transaction (withdraw) for selection account
Route::post	 ('/Transaction/{id}/deposite'			, 'testCon@addDeposite' 	);//insert transaction (deposite) for selection account
Route::post	 ('/Transaction/{id}/transfer/{id2}'	, 'testCon@addTransfer' 	);//insert transaction (transfer) for selection account
Route::get   ('/Transaction/{id}/withdraw'			, 'testCon@getWithdraw' 	);//get transaction (withdraw) for selection account
Route::get	 ('/Transaction/{id}/deposite'			, 'testCon@getDeposite' 	);//get transaction (deposite) for selection account
Route::get	 ('/Transaction/{id}/transfer'			, 'testCon@getTransfer' 	);//get transaction (transfer) for selection account
Route::get	 ('/Transaction/{id}'					, 'testCon@getTransaction' 	);//get all transaction for selection account
//Route::get	 ('/Transaction/{id}/month/{month}'		, 'testCon@getTransactionInMonth');//get all transaction for selection account for each month //suggestion
//Route::put	 ('/Transaction/{id}'				    , 'testCon@editTransaction' );//transaction should not be edit
//Route::delete('/Transaction/{id}'				    , 'testCon@removeTransaction');//transaction should not be delete
Route::post  ('/ATM'								, 'testCon@addATM' 			);//add new ATM
Route::get   ('/ATM'								, 'testCon@getAllATM' 		);//get money in selection ATM
Route::get   ('/ATM/{id}'							, 'testCon@getATM' 			);//get money in selection ATM
Route::put	 ('/ATM/{id}/withdraw/'					, 'testCon@withdrawATM'		);//withdraw money in selection ATM
Route::put	 ('/ATM/{id}/deposite/'					, 'testCon@depositeATM'		);//deposite money in selection ATM
Route::delete('/ATM'								, 'testCon@removeAllATM'	);//delete all ATM
Route::delete('/ATM/{id}'							, 'testCon@removeATM' 		);//delete selection ATM