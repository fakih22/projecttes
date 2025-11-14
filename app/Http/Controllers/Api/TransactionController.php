<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;
use App\Models\Transaction;
use App\Models\TransactionItem;   // <— penting
use App\Models\Donat;

class TransactionController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = (bool) config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }
    public function store(Request $request)
    {
        $customer = $request->user(); // Sanctum

        $data = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.donat_id' => 'required|exists:donats,id',
            'items.*.qty' => 'required|integer|min:1',
            'nama_penerima' => 'required|string|max:255',
            'no_hp' => 'required|string|max:40',
            'alamat' => 'required|string',
        ]);

        // Ambil data donat yang dibutuhkan
        $donatIds = collect($data['items'])->pluck('donat_id')->unique()->values();
        $donats = Donat::whereIn('id', $donatIds)->get()->keyBy('id');

        // Validasi stok awal
        foreach ($data['items'] as $it) {
            $d = $donats[$it['donat_id']] ?? null;
            if (!$d) {
                return response()->json([
                    'success' => false,
                    'message' => "Donat ID {$it['donat_id']} tidak ditemukan.",
                ], 422);
            }
            if ((int) $d->stok < (int) $it['qty']) {
                return response()->json([
                    'success' => false,
                    'message' => "Stok tidak mencukupi untuk {$d->nama} (tersedia: {$d->stok}).",
                ], 422);
            }
        }

        // Susun item_details (untuk Midtrans) & hitung total
        $itemDetails = [];
        $totalDecimal = '0.00'; // pakai bcmath string

        foreach ($data['items'] as $it) {
            $d = $donats[$it['donat_id']];
            $qty = (int) $it['qty'];

            $hargaStr = (string) $d->harga;             // decimal(15,2) as string
            $subTotalStr = bcmul($hargaStr, (string) $qty, 2);
            $totalDecimal = bcadd($totalDecimal, $subTotalStr, 2);

            $itemDetails[] = [
                'id' => (string) $d->id,
                'price' => (int) round((float) $d->harga),  // Midtrans harus INT
                'quantity' => $qty,
                'name' => $d->nama,
            ];
        }

        $grossAmount = (int) round((float) $totalDecimal); // INT utk Midtrans
        $kode = 'INV-' . strtoupper(Str::random(10));

        DB::beginTransaction();
        try {
            $firstDonatId = (int) ($data['items'][0]['donat_id'] ?? null);

            $trx = Transaction::create([
                'customer_id' => $customer?->id,
                'donat_id' => $firstDonatId, // tidak dipakai untuk stok
                'qty' => array_sum(array_map(fn($i) => (int) $i['qty'], $data['items'])),
                'total_harga' => $totalDecimal,           // simpan tetap desimal
                'status' => 'pending',
                'kode_transaksi' => $kode,
                'snap_token' => null,
                'nama_penerima' => $data['nama_penerima'],
                'no_hp' => $data['no_hp'],
                'alamat' => $data['alamat'],
            ]);

            foreach ($data['items'] as $it) {
                $d = $donats[$it['donat_id']];
                $qty = (int) $it['qty'];

                $priceStr = (string) $d->harga;
                $subtotalStr = bcmul($priceStr, (string) $qty, 2);

                TransactionItem::create([
                    'transaction_id' => $trx->id,
                    'donat_id' => $d->id,
                    'qty' => $qty,
                    'price' => $priceStr,    // boleh string utk decimal
                    'subtotal' => $subtotalStr, // boleh string utk decimal
                ]);
            }

            // Payload ke Midtrans
            $params = [
                'transaction_details' => [
                    'order_id' => $trx->kode_transaksi,
                    'gross_amount' => $grossAmount,
                ],
                'customer_details' => [
                    'first_name' => $trx->nama_penerima,
                    'email' => $customer?->email ?? $request->input('email', 'guest@example.com'),
                    'phone' => $trx->no_hp,
                    'shipping_address' => ['address' => $trx->alamat],
                ],
                'item_details' => $itemDetails,
            ];

            $snapToken = Snap::getSnapToken($params);
            $trx->update(['snap_token' => $snapToken]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dibuat',
                'transaction' => $trx->load(['items.donat']), // kirim juga detail item
                'snap_token' => $snapToken,
                'snap_url' => "https://app.sandbox.midtrans.com/snap/v2/vtweb/{$snapToken}",
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Create transaction error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat transaksi',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function notification(Request $request)
    {
        // Midtrans SDK parse body & verifikasi signature otomatis
        $notif = new Notification();

        $orderId = $notif->order_id;
        $transactionStatus = $notif->transaction_status;
        $fraudStatus = $notif->fraud_status; 

        Log::info('[Midtrans] notif', $request->all());

        /** @var Transaction|null $trx */
        $trx = Transaction::with('items')->where('kode_transaksi', $orderId)->first();
        if (!$trx) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        $mapped = match ($transactionStatus) {
            'capture' => ($fraudStatus === 'challenge') ? 'challenge' : 'success',
            'settlement' => 'success',
            'pending' => 'pending',
            'expire' => 'expired',
            'cancel', 'deny' => 'failed',
            default => $trx->status,
        };

        DB::beginTransaction();
        try {
            $becameSuccess = ($trx->status !== 'success' && $mapped === 'success');

            $trx->status = $mapped;
            $trx->save();

            if ($becameSuccess) {
                foreach ($trx->items as $line) {
                    // kunci baris agar aman dari race condition
                    $donat = Donat::lockForUpdate()->find($line->donat_id);
                    if (!$donat) {
                        Log::warning('[Midtrans] donat hilang saat update stok', [
                            'donat_id' => $line->donat_id,
                            'order_id' => $orderId
                        ]);
                        continue;
                    }

                    $stokAwal = (int) $donat->stok;
                    $reduceBy = min((int) $line->qty, max(0, $stokAwal)); // cegah minus

                    if ($reduceBy > 0) {
                        $donat->stok = max(0, $stokAwal - $reduceBy);
                        $donat->save();
                    }

                    Log::info('[Midtrans] stok dikurangi per-item', [
                        'order_id' => $orderId,
                        'donat_id' => $donat->id,
                        'qty_line' => (int) $line->qty,
                        'stok_awal' => $stokAwal,
                        'stok_akhir' => (int) $donat->stok,
                    ]);
                }
            }

            DB::commit();
            return response('OK', 200);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('[Midtrans][notif] ' . $e->getMessage(), [
                'order_id' => $orderId,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['message' => 'Server error'], 500);
        }
    }

    public function index(Request $request)
    {
        $customer = $request->user();

        $transactions = Transaction::with(['donat', 'items.donat'])
            ->when($customer, fn($q) => $q->where('customer_id', $customer->id))
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $transactions,
        ]);
    }

    public function getTransactionById($id)
    {
        $trx = Transaction::with(['donat', 'items.donat'])->find($id);

        if (!$trx) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $trx,
        ]);
    }
}
