<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPickupSession
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $cart = session('pickup_cart', []);
        $address = session('pickup_address');
        $orderType = session('pickup_order_type', 'sell');

        // 1. Jika keranjang aki reject kosong atau alamat belum diisi, langsung lempar ke beranda
        if (empty($cart) || empty($address)) {
            return redirect('/');
        }

        // 2. Jika coba akses /trade-in tapi tipe order bukan trade_in
        if ($request->is('trade-in') && $orderType !== 'trade_in') {
            return redirect('/');
        }

        // 3. Jika coba akses /identity untuk trade_in tapi belum pilih aki baru
        if (($request->is('identity') || $request->is('user/identitas')) && $orderType === 'trade_in') {
            $tradeInCart = session('pickup_trade_in_cart', []);
            if (empty($tradeInCart)) {
                return redirect('/trade-in');
            }
        }

        return $next($request);
    }
}
