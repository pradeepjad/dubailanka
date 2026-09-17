<?php
namespace App\Actions\Store;
use App\Models\Store; use App\Models\User; use Illuminate\Support\Facades\DB;
class ChangeStoreStatus { public function execute(Store $store,string $to,User $actor,?string $note=null):Store { return DB::transaction(function()use($store,$to,$actor,$note){$from=$store->status;$store->update(['status'=>$to]);$store->statusHistories()->create(['actor_user_id'=>$actor->id,'from_status'=>$from,'to_status'=>$to,'note'=>$note]);return $store->fresh();}); } }
