<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
//use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Model\Account;
use App\Model\ATM;
use Validator;
use DB;
use DateTime;
class testCon extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

	public function addAccount(Request $request){
		$check = Validator::make($request->all(), ['Name' => 'required' , 'Amount' => 'required|integer|min:1']);
		if($check->fails()){
			if(!($request->has('Name'))){
				return response(array('message'=>'Please insert Name'));
			}
			else if(!($request->has('Amount'))){
				return response(array('message'=>'Please insert Amount value'));
			}
			else if($request->get('Amount') < 1){
				return response(array('message'=>'Please insert positive Amount value'));
			}
		}else{
			$account = new Account();
			$post = $request->all();
			//remove csrf token
			if(array_key_exists('_token', $post)){
				unset($post['_token']);
			}
			$account->addAccount($post);
			return response(array('message'=>'success'));
		}
    }
	public function getAccount($id){
		$account = new Account();
		$status = $account->getAccount($id);
		if(isset($status[0])){
			return response($status);
		}
		else{
			return response(array('message'=>'Account not found'));
		}
    }
	public function getAllAccount(){
		$account = new Account();
		$status = $account->getAllAccount();
		if(isset($status[0])){
			return response($status);
		}
		else{
			return response(array('message'=>'Account not found'));
		}
    }
	public function editAccount(Request $request,$id){
		$account = new Account();
		$post = $request->all();
		if(array_key_exists('_token', $post)){
			unset($post['_token']);
		}
		if($request->has('Amount')){
			return response(array('message'=>'Can not edit amount value'));
		}else{
			$status = $account->editAccount($post,$id);
			if(isset($status[0])){
				return response($status);
			}
			else{
				return response(array('message'=>'Account not found'));
			}
		}
    }	
	public function removeAccount($id){
		$account = new Account();
		$rd = $account->removeAccount($id);
		return response($rd);
    }
	public function removeAllAccount(){
		$account = new Account();
		$rd = $account->removeAllAccount();
		return response($rd);
    }
	public function addWithdraw(Request $request ,$id){
		$account = new Account();
		$post = $request->all();
		$check = Validator::make($request->all(), ['Amount' => 'required|integer|min:1|max:'.$account->getAccount($id)[0]['Amount']]);
		if($check->fails()){
			if(!($request->has('Amount'))){
				return response(array('message'=>'Please insert Amount value'));
			}
			else if($request->get('Amount') < 1){
				return response(array('message'=>'Please insert positive Amount value'));
			}
			else if($request->get('Amount') > $account->getAccount($id)[0]['Amount']){
				return response(array('message'=>'Amount value is more than current amount'));
			}
		}
		else{
			if(array_key_exists('_token', $post)){
				unset($post['_token']);
			}
			$dt = new dateTime();
			$input = array('Type'=>'Withdraw') + $post + array('Date' => $dt->format('Y-m-d H:i:s'));
			$status = $account->addTransaction($input,$id);
			//update amount value
			$newamount = intval($account->getAccount($id)[0]['Amount'] - $post['Amount']);
			$post['Amount'] = strval($newamount);
			$status = $account->editAccount($post,$id);
		}
    }
	public function addDeposite(Request $request ,$id){
		$account = new Account();
		$post = $request->all();
		$check = Validator::make($request->all(), ['Amount' => 'required|integer|min:1']);
		if($check->fails()){
			if(!($request->has('Amount'))){
				return response(array('message'=>'Please insert Amount value'));
			}
			else if($request->get('Amount') < 1){
				return response(array('message'=>'Please insert positive Amount value'));
			}
		}
		else{
			if(array_key_exists('_token', $post)){
				unset($post['_token']);
			}
			$dt = new dateTime();
			$input = array('Type'=>'Deposite') + $post + array('Date' => $dt->format('Y-m-d H:i:s'));
			$status = $account->addTransaction($input,$id);
			$newamount = intval($account->getAccount($id)[0]['Amount'] + $post['Amount']);
			$post['Amount'] = strval($newamount);
			$status = $account->editAccount($post,$id);
		}
    }
	public function addTransfer(Request $request ,$id , $id2){
		$account = new Account();
		$post = $request->all();
		if(isset($account->getAccount($id)[0]) and isset($account->getAccount($id2)[0])){
			$check = Validator::make($request->all(), ['Amount' => 'required|integer|min:1|max:'.$account->getAccount($id)[0]['Amount']]);
			if($check->fails()){
				if(!($request->has('Amount'))){
					return response(array('message'=>'Please insert Amount value'));
				}
				else if($request->get('Amount') < 1){
					return response(array('message'=>'Please insert positive Amount value'));
				}
				else if($request->get('Amount') > $account->getAccount($id)[0]['Amount']){
					return response(array('message'=>'Amount value is more than current amount'));
				}
			}
			else{
				if(array_key_exists('_token', $post)){
					unset($post['_token']);
				}
				$dt = new dateTime();
				$input = array('Type'=>'Transfer') + $post + array('To'=>$id2)  + array('Date' => $dt->format('Y-m-d H:i:s'));
				$status = $account->addTransaction($input,$id);
				$newamount = intval($account->getAccount($id)[0]['Amount'] - $post['Amount']);
				$newamount2 = intval($account->getAccount($id2)[0]['Amount'] + $post['Amount']);
				$post['Amount'] = strval($newamount);
				$status = $account->editAccount($post,$id);
				$post['Amount'] = strval($newamount2);
				$status = $account->editAccount($post,$id2);
			}
		}
    }
	public function getWithdraw($id){
		$account = new Account();
		$data = array_filter($account->getAccount($id)[0]['Transaction'], function ($trans){ return $trans['Type'] == 'Withdraw';});
		return response($data);
    }
	public function getDeposite($id){
		$account = new Account();
		$data = array_filter($account->getAccount($id)[0]['Transaction'], function ($trans){ return $trans['Type'] == 'Deposite';});
		return response($data);
    }
	public function getTransfer($id){
		$account = new Account();
		$data = array_filter($account->getAccount($id)[0]['Transaction'], function ($trans){ return $trans['Type'] == 'Transfer';});
		return response($data);
    }
	public function getTransaction($id){
		$account = new Account();
		return response($account->getAccount($id)[0]['Transaction']);
    }

	public function removeTransaction($id){
		$account = new Account();
		return response($account->getAccount($id)[0]['Transaction']);
    }
	public function addATM(Request $request){
		$atm = new ATM();
		$check = Validator::make($request->all(), ['Place' => 'required' , 'Amount' => 'required|integer|min:1']);
		if($check->fails()){
			if(!($request->has('Place'))){
				return response(array('message'=>'Please insert Place'));
			}
			else if(!($request->has('Amount'))){
				return response(array('message'=>'Please insert Amount value'));
			}
			else if($request->get('Amount') < 1){
				return response(array('message'=>'Please insert positive Amount value'));
			}
		}else{
			$post = $request->all();
			//remove csrf token
			if(array_key_exists('_token', $post)){
				unset($post['_token']);
			}
			$atm->addATM($post);
			return response(array('message'=>'success'));
		}
    }
	public function getATM($id){
		$atm = new ATM();
		$status = $atm->getATM($id);
		if(isset($rd[0])){
			return response($status);
		}
		else{
			return response(array('message'=>'ATM not found'));
		}
    }
	public function getAllATM(){
		$atm = new ATM();
		$status = $atm->getAllATM();
		if(isset($status[0])){
			return response($status);
		}
		else{
			return response(array('message'=>'ATM not found'));
		}
    }
	public function withdrawATM(Request $request , $id){
		$atm = new ATM();
		$post = $request->all();
		if(array_key_exists('_token', $post)){
			unset($post['_token']);
		}		
		$newmount = intval($atm->getATM($id)[0]['Amount'] - $post['Amount']);
		$post['Amount'] = strval($newmount);
		$status = $atm->editATM($post,$id);
		if(isset($status[0])){
			return response($status);
		}
		else{
			return response(array('message'=>'ATM not found'));
		}
    }
	public function depositeATM(Request $request , $id){
		$atm = new ATM();
		$post = $request->all();
		if(array_key_exists('_token', $post)){
			unset($post['_token']);
		}		
		$newmount = intval($atm->getATM($id)[0]['Amount'] + $post['Amount']);
		$post['Amount'] = strval($newmount);
		$status = $atm->editATM($post,$id);
		if(isset($status[0])){
			return response($status);
		}
		else{
			return response(array('message'=>'ATM not found'));
		}
    }
	public function removeAllATM(){
		$atm = new ATM();
		$status = $atm->removeAllATM();
		return response($status);
    }
	public function removeATM($id){
		$atm = new Account();
		$status = $atm->removeATM($id);
		return response($status);		
    }
}
