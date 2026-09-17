<?php
namespace App\Http\Controllers\Auth; use App\Http\Controllers\Controller; use Illuminate\Http\RedirectResponse; use Illuminate\Http\Request; use Illuminate\Support\Facades\Auth;
class LogoutController extends Controller { public function __invoke(Request $r):RedirectResponse{Auth::logout();$r->session()->invalidate();$r->session()->regenerateToken();return redirect()->route('home');} }
