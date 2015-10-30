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
	public function getATM($id){
		$rd = DB::collection($this->getTable())->where('_id',$id);
		return $rd->get();
    }
	public function getAllATM(){
		$rd = DB::collection($this->getTable());
		return $rd->get();
    }
	public function editATM($post,$id){
		$rd = DB::collection($this->getTable())->where('_id',$id);
		$rd->update($post);
		return $rd->get();
    }
	public function removeATM($id){
		$rd = DB::collection($this->getTable())->where('_id',$id);
		$rd->delete();
		return $rd->get();
    }
	public function removeAllATM(){
		$rd = DB::collection($this->getTable());
		$rd->delete();
		return $rd->get();
    }
}