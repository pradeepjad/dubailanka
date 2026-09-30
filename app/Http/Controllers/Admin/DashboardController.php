<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\BusinessEntity;
use App\Models\SellerOnboardingHandover;
use App\Models\Store;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
class DashboardController extends Controller{
 public function __invoke(Request $request):Response{
  $admin=$request->user()->platformAdmin;
  $stores=Store::query()->when(!$admin?->is_super_admin,fn($q)=>$q->whereHas('adminAssignment',fn($a)=>$a->where('platform_admin_id',$admin?->id)));
  $counts=(clone $stores)->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total','status');
  $unassigned=$admin?->is_super_admin ? BusinessEntity::whereDoesntHave('memberships',fn($q)=>$q->where('is_primary_owner',true))->count() : null;
  return Inertia::render('Admin/Dashboard',['counts'=>$counts,'pendingHandovers'=>$admin?->is_super_admin?SellerOnboardingHandover::where('status',SellerOnboardingHandover::STATUS_PENDING)->count():null,'unassignedBusinesses'=>$unassigned,'isSuperAdmin'=>(bool)$admin?->is_super_admin]);
 }
}
