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
				return response(['status'=>'Please insert Name']);
			}
			else if(!($request->has('Amount'))){
				return response(['status'=>'Please insert Amount value']);
			}
			else if($request->get('Amount') < 1){
				return response(['status'=>'Please insert positive Amount value']);
			}
		}else{
			$account = new Account();
			$post = $request->all();
			//remove csrf token
			if(array_key_exists('_token', $post)){
				unset($post['_token']);
			}
			$account->addAccount($post);
			return response(['status'=>'success']);
		}
    }
	public function getAccount($id){
		$account = new Account();
		$data = $account->getAccount($id);
		if(isset($data[0])){
			return response($data);
		}else{
			return response(['status'=>'Account not found']);
		}
    }
	public function getAllAccount(){
		$account = new Account();
		$data = $account->getAllAccount();
		if(isset($data[0])){
			return response($data);
		}else{
			return response(['status'=>'Account not found']);
		}
    }
	public function editAccount(Request $request,$id){
		$account = new Account();
		$post = $request->all();
		if(array_key_exists('_token', $post)){
			unset($post['_token']);
		}
		if($request->has('Amount')){
			return response(['status'=>'Can not edit amount value']);
		}else{
			$data = $account->getAccount($id);
			if(isset($data[0])){
				$status = $account->editAccount($post,$id);
				return response(['status'=>'success']);
			}else{
				return response(['status'=>'Account not found']);
			}
		}
    }	
	public function removeAccount($id){
		$account = new Account();
		$data = $account->getAccount($id);
		if(isset($data[0])){
			$rd = $account->removeAccount($id);
			return response(['status'=>'success']);
		}else{
			return response(['status'=>'Account not found']);
		}
    }
	public function removeAllAccount(){
		$account = new Account();
		$data = $account->getAllAccount();
		if(isset($data[0])){
			$rd = $account->removeAllAccount();
			return response(['status'=>'success']);
		}else{
			return response(['status'=>'Account not found']);
		}
    }
	public function addWithdraw($id , $amount){
		$account = new Account();
		$data = $account->getAccount($id);
		if(isset($data[0])){
			$check = Validator::make(['Amount' => $amount], ['Amount' => 'required|integer|min:1|max:'.$data[0]['Amount']]);
			if($check->fails()){
				if($amount < 1){
					return ['status'=>'Please insert positive/integer number for Amount values'];
				}
				else if($amount > $account->getAccount($id)[0]['Amount']){
					return ['status'=>'Amount value is more than current amount'];
				}
			}else{
				$dt = new dateTime();
				$input = ['Type'=>'Withdraw'] + ['Amount' => $amount] + ['Date' => $dt->format('Y-m-d H:i:s')];
				$status = $account->addTransaction($input,$id);
				//update amount value
				$newamount = intval($data[0]['Amount'] - $amount);
				$status = $account->editAccount(['Amount' => strval($newamount)],$id);
				return ['status'=>'success'];
			}
		}else{
			return ['status'=>'Account not found'];
		}
    }
	public function addDeposite($id , $amount){
		$account = new Account();
		$data = $account->getAccount($id);
		if(isset($data[0])){
			$check = Validator::make(['Amount' => $amount], ['Amount' => 'required|integer|min:1']);
			if($check->fails()){
				if(!($request->has('Amount'))){
					return ['status'=>'Please insert positive/integer number for Amount values'];
				}
				else if($request->get('Amount') < 1){
					return ['status'=>'Please insert positive Amount value'];
				}
			}else{
				$dt = new dateTime();
				$input = ['Type'=>'Deposite'] + ['Amount' => $amount] + ['Date' => $dt->format('Y-m-d H:i:s')];
				$status = $account->addTransaction($input,$id);
				$newamount = intval($data[0]['Amount'] + $amount);
				$status = $account->editAccount(['Amount' => strval($newamount)],$id);
				return ['status'=>'success'];
			}
		}else{
			return ['status'=>'Account not found'];
		}
    }
	public function addTransfer($id , $id2 , $amount){
		$account = new Account();
		$data = $account->getAccount($id);
		$data2 = $account->getAccount($id2);
		if(isset($data[0]) and isset($data2[0])){
			$check = Validator::make(['Amount' => $amount], ['Amount' => 'required|integer|min:1|max:'.$data[0]['Amount']]);
			if($check->fails()){
				if(!($request->has('Amount'))){
					return response(['status'=>'Please insert Amount value']);
				}
				else if($request->get('Amount') < 1){
					return response(['status'=>'Please insert positive Amount value']);
				}
				else if($request->get('Amount') > $data[0]['Amount']){
					return response(['status'=>'Amount value is more than current amount']);
				}
			}else{
				$dt = new dateTime();
				$input = ['Type'=>'Transfer'] + ['Amount' => $amount] + ['To'=>$id2]  + ['Date' => $dt->format('Y-m-d H:i:s')];
				$status = $account->addTransaction($input,$id);
				$newamount = intval($data[0]['Amount'] - $amount);
				$newamount2 = intval($data2[0]['Amount'] + $amount);
				$status = $account->editAccount(['Amount' => strval($newamount)],$id);
				$status = $account->editAccount(['Amount' => strval($newamount2)],$id2);
				return response(['status'=>'success']);
			}
		}
    }

	public function getWithdraw($id){
		$account = new Account();
		$data = $account->getAccount($id);
		if(isset($data[0])){
			$data = array_filter($data[0]['Transaction'], function ($trans){ return $trans['Type'] == 'Withdraw';});
			return response($data);
		}else{
			return response(['status'=>'Account not found']);
		}
    }
	public function getDeposite($id){
		$account = new Account();
		$data = $account->getAccount($id);
		if(isset($data[0])){
			$data = array_filter($data[0]['Transaction'], function ($trans){ return $trans['Type'] == 'Deposite';});
			return response($data);
		}else{
			return response(['status'=>'Account not found']);
		}
    }
	public function getTransfer($id){
		$account = new Account();
		$data = $account->getAccount($id);
		if(isset($data[0])){
			$data = array_filter($data[0]['Transaction'], function ($trans){ return $trans['Type'] == 'Transfer';});
			return response($data);
		}else{
			return response(['status'=>'Account not found']);
		}
    }
	public function getTransaction($id){
		$account = new Account();
		$data = $account->getAccount($id);
		if(isset($data[0])){
			return response($data[0]['Transaction']);
		}else{
			return response(['status'=>'Account not found']);
		}
    }

	public function addATM(Request $request){
		$atm = new ATM();
		
		$check = Validator::make($request->all(), ['Place' => 'required' , 'Amount' => 'required|integer|min:1']);
		if($check->fails()){
			if(!($request->has('Place'))){
				return response(['status'=>'Please insert Place']);
			}
			else if(!($request->has('Amount'))){
				return response(['status'=>'Please insert Amount value']);
			}
			else if($request->get('Amount') < 1){
				return response(['status'=>'Please insert positive Amount value']);
			}
		}else{
			$post = $request->all();
			$data = $atm->getATM($post['Place']);
			if(isset($data[0])){
				return response(['status'=>'This place already exist']);
			}
			//remove csrf token
			if(array_key_exists('_token', $post)){
				unset($post['_token']);
			}
			$atm->addATM($post);
			return response(['status'=>'success']);
		}
    }
	public function getATM($aid){
		$atm = new ATM();
		$data = $atm->getATM($aid);
		if(isset($data[0])){
			return response($data);
		}else{
			return response(['status'=>'ATM not found']);
		}
    }
	public function getAllATM(){
		$atm = new ATM();
		$data = $atm->getAllATM();
		if(isset($data[0])){
			return response($data);
		}else{
			return response(['status'=>'ATM not found']);
		}
    }

	public function withdrawATM($aid , $id , $amount){
		$atm = new ATM();
		$data = $atm->getATM($aid);
		if(isset($data[0])){
			$status = $this->addWithdraw($id , $amount);
			if($status['status'] == 'success'){
				$newamount = intval($atm->getATM($aid)[0]['Amount'] - $amount);
				$status = $atm->editATM(['Amount' => strval($newamount)],$aid);
				return response(['status'=>'success']);
			}else{
				return response($status['status']);
			}
		}else{
			return response(['status'=>'ATM not found']);
		}		
    }
	public function depositeATM($aid , $id , $amount){
		$atm = new ATM();
		$data = $atm->getATM($aid);
		if(isset($data[0])){
			$status = $this->addDeposite($id, $amount);
			if($status['status'] == 'success'){
				$newamount = intval($atm->getATM($aid)[0]['Amount'] + $amount);
				$status = $atm->editATM(['Amount' => strval($newamount)],$aid);			
				return response(['status'=>'success']);
			}else{
				return response($status['status']);
			}	
		}else{
			return response(['status'=>'ATM not found']);
		}
    }
	
	public function removeAllATM(){
		$atm = new ATM();
		$data = $atm->getAllATM();
		if(isset($data[0])){
			$status = $atm->removeAllATM();
			if(isset($status[0])){
				return response(['status'=>'success']);
			}else{
				return response(['status'=>'DB delete failed']);
			}
		}else{
			return response(['status'=>'ATM not found']);
		}
    }
	public function removeATM($aid){
		$atm = new Account();
		$data = $atm->getATM($aid);
		if(isset($data[0])){
			$status = $atm->removeATM($aid);
			if(isset($status[0])){
				return response(['status'=>'success']);
			}else{
				return response(['status'=>'DB delete failed']);
			}
		}else{
			return response(['status'=>'ATM not found']);
		}			
    }
}
