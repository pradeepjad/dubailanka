<?php
namespace App\Http\Controllers\Seller;
use App\Actions\Store\ChangeStoreStatus; use App\Http\Controllers\Controller; use App\Models\Store; use Illuminate\Http\RedirectResponse; use Illuminate\Http\Request;
class StoreSubmissionController extends Controller {
 public function submit(Request $request,Store $store,ChangeStoreStatus $change):RedirectResponse{$this->access($request,$store);abort_unless(in_array($store->status,[Store::STATUS_DRAFT,Store::STATUS_NEEDS_CHANGES],true),422);$change->execute($store,Store::STATUS_PENDING,$request->user());return back()->with('success','Store submitted for Dubai Lanka review.');}
 public function withdraw(Request $request,Store $store,ChangeStoreStatus $change):RedirectResponse{$this->access($request,$store);abort_unless($store->status===Store::STATUS_PENDING,422);$change->execute($store,Store::STATUS_DRAFT,$request->user(),'Seller withdrew submission to edit.');return to_route('seller.stores.edit',$store)->with('success','Submission withdrawn. You can edit the store again.');}
 private function access(Request $r,Store $s):void{abort_unless($r->user()->stores()->whereKey($s->id)->exists(),403);}
}
