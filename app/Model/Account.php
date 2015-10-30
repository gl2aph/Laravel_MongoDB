<?php
namespace App\Model;

use DB;
use Jenssegers\Mongodb\Model as Eloquent;
class Account extends Eloquent {

    protected $collection = 'Account';
	
    public function addAccount($post){
		if($this->insert($post)){
			return true;
		}else{
			return false;
		}
    }	
	public function getAccount($id){
		$rd = DB::collection($this->getTable())->where('_id',$id);
		return $rd->get();
    }
	public function getAllAccount(){
		$rd = DB::collection($this->getTable());
		return $rd->get();
    }
	public function editAccount($post,$id){
		$rd = DB::collection($this->getTable())->where('_id',$id);
		$rd->update($post);
		return $rd->get();
    }	
	public function removeAccount($id){
		$rd = DB::collection($this->getTable())->where('_id',$id);
		$rd->delete();
		return $rd->get();
    }
	public function removeAllAccount(){
		$rd = DB::collection($this->getTable());
		$rd->delete();
		return $rd->get();
    }
	public function addTransaction($input,$id){
		$rd = DB::collection($this->getTable())->where('_id',$id);
		$rd->push('Transaction',$input);
		return $rd->get();
	}
}