<?php
namespace App\Model;

use DB;
use Jenssegers\Mongodb\Model as Eloquent;

class ATM extends Eloquent {

    protected $collection = 'ATM';
	
	public function addATM($post){
		if($this->insert($post)){
			return true;
		}else{
			return false;
		}
    }	
	public function getATM($aid){
		$rd = DB::collection($this->getTable())->where('_id',$aid)->orwhere('Place',$aid);
		return $rd->get();
    }
	public function getAllATM(){
		$rd = DB::collection($this->getTable());
		return $rd->get();
    }
	public function editATM($post,$aid){
		$rd = DB::collection($this->getTable())->where('_id',$aid);
		$rd->update($post);
		return $rd->get();
    }
	public function removeATM($aid){
		$rd = DB::collection($this->getTable())->where('_id',$aid);
		$rd->delete();
		return $rd->get();
    }
	public function removeAllATM(){
		$rd = DB::collection($this->getTable());
		$rd->delete();
		return $rd->get();
    }
}