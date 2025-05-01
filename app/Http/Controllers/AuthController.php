<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Pengguna;
use App\Models\Pembeli;
use App\Models\Pegawai;
use App\Models\Penitip;
use App\Models\Organisasi;
use App\Models\Hunter;
use App\Services\FcmService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function registerPembeli(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:penggunas',
            'username' => 'required|unique:penggunas',
            'password' => 'required|confirmed',
            'nama_pembeli' => 'required|string'
        ]);

        $pembeli = Pembeli::create([
            'nama_pembeli' => $request->nama_pembeli,
            'poin_reward' => 0
        ]);
        $pengguna = Pengguna::create([
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'id_pembeli' => $pembeli->id_pembeli
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Pembeli berhasil terdaftar',
            'data' => [
                'pengguna' => $pengguna,
                'pembeli' => $pembeli
            ]
        ], 201);
    }

    public function registerPegawai(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:penggunas',
            'username' => 'required|unique:penggunas',
            'password' => 'required|confirmed',
            'nama_pegawai' => 'required|string',
            'tanggal_lahir' => 'required|date',
            'id_jabatan' => 'required|exists:jabatans,id_jabatan'
        ]);

        $pegawai = Pegawai::create([
            'nama_pegawai' => $request->nama_pegawai,
            'tanggal_lahir' => $request->tanggal_lahir,
            'id_jabatan' => $request->id_jabatan
        ]);

        $pengguna = Pengguna::create([
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'id_pegawai' => $pegawai->id_pegawai
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Pegawai berhasil terdaftar',
            'data' => [
                'pengguna' => $pengguna,
                'pegawai' => $pegawai
            ]
        ]);
    }

    public function registerPenitip(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:penggunas',
            'username' => 'required|unique:penggunas',
            'password' => 'required|confirmed',
            'nama_penitip' => 'required|string',
            'no_telepon' => 'required|string',
            'alamat_penitip' => 'required|string',
            'foto_ktp' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'no_ktp' => 'required|string',
        ]);

        $penitip = Penitip::create([
            'nama_penitip' => $request->nama_penitip,
            'no_telepon' => $request->no_telepon,
            'alamat_penitip' => $request->alamat_penitip,
            'saldo' => 0,
            'no_ktp' => $request->no_ktp,
            'foto_ktp' => $request->file('foto_ktp')->store('penitip', 'public')
        ]);

        $pengguna = Pengguna::create([
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'id_penitip' => $penitip->id_penitip
        ]);
        return response()->json([
            'status' => true,
            'message' => 'Penitip berhasil terdaftar',
            'data' => [
                'pengguna' => $pengguna,
                'penitip' => $penitip
            ]
        ]);
    }

    public function registerOrganisasi(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:penggunas',
            'username' => 'required|unique:penggunas',
            'password' => 'required|confirmed',
            'nama_organisasi' => 'required|string',
            'alamat_organisasi' => 'required|string',
        ]);

        $organisasi = Organisasi::create([
            'nama_organisasi' => $request->nama_organisasi,
            'alamat_organisasi' => $request->alamat_organisasi
        ]);

        $pengguna = Pengguna::create([
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'id_organisasi' => $organisasi->id_organisasi
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Organisasi berhasil terdaftar',
            'data' => [
                'pengguna' => $pengguna,
                'organisasi' => $organisasi
            ]
        ]);
    }

    public function registerHunter(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:penggunas',
            'username' => 'required|unique:penggunas',
            'password' => 'required|confirmed',
            'nama_hunter' => 'required|string',
            'no_telepon' => 'required|string',
        ]);

        $hunter = Hunter::create([
            'nama_hunter' => $request->nama_hunter,
            'saldo' => 0,
            'no_telepon' => $request->no_telepon
        ]);

        $pengguna = Pengguna::create([
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'id_hunter' => $hunter->id_hunter
        ]);
        return response()->json([
            'status' => true,
            'message' => 'Hunter berhasil terdaftar',
            'data' => [
                'pengguna' => $pengguna,
                'hunter' => $hunter
            ]
        ]);
    }

    public function login(Request $resquest)
    {
        $resquest->validate([
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        $pengguna = Pengguna::where('username', $resquest->username)->first();

        if (!$pengguna || !Hash::check($resquest->password, $pengguna->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Username atau password salah'
            ], 401);
        }

        $token = $pengguna->createToken('auth_token')->plainTextToken;

        $role = $pengguna->role;

        return response()->json([
            'status' => true,
            'message' => 'Login berhasil',
            'data' => [
                'pengguna' => $pengguna
            ],
            'access_token' => $token,
            'token_type' => 'Bearer',
            'role' => $role,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        $response = FcmService::sendNotification(
            $request->user()->fcm_token,
            'Logout',
            'Anda telah logout dari aplikasi'
        );
        
        if (!$response['success']) {
            Log::error('Failed to send FCM notification: ' . $response['message']);
            return response()->json([
                'status' => false,
                'message' => 'Logout berhasil, tetapi gagal mengirim notifikasi'
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Logout berhasil'
        ]);
    }

    public function changePasswordPegawai(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'tanggalLahir' => 'required|date',
        ]);

        $pengguna = Pengguna::where('username', $request->username)->first();
        if (!$pengguna) {
            return response()->json([
                'status' => false,
                'message' => 'Username tidak ditemukan'
            ], 404);
        }

        $pegawai = Pegawai::where('id_pegawai', $pengguna->id_pegawai)
            ->where('tanggal_lahir', $request->tanggalLahir)
            ->first();

        if (!$pegawai) {
            return response()->json([
                'status' => false,
                'message' => 'Tanggal lahir tidak sesuai'
            ], 404);
        }

        $passwordBaru = $pegawai->tanggal_lahir;
        $pengguna->password = Hash::make($passwordBaru);
        $pengguna->save();

        return response()->json([
            'status' => true,
            'message' => 'Password berhasil diubah'
        ]);
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
        ]);

        $user = Pengguna::where('email', $request->email)->first();
        $otp = rand(1000, 9999);

        $user->otp = $otp;
        $user->otp_expired_at = now()->addMinutes(10);
        $user->save();

        Mail::to($user->email)->send(new \App\Mail\SendOtpMail($otp));

        return response()->json([
            'status' => true,
            'message' => 'OTP berhasil dikirim ke email',
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required'
        ]);
        
        $user = Pengguna::where('email', $request->email)->first();

        if (!$user || $user->otp !== $request->otp || now()->gt($user->otp_expired_at)) {
            return back()->withErrors(['otp' => 'Kode OTP salah atau kedaluwarsa']);
        }

        return response()->json([
            'status' => true,
            'message' => 'OTP valid',
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = Pengguna::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->otp = null;
        $user->otp_expired_at = null;
        $user->save();

        return response()->json(
            [
                'status' => true,
                'message' => 'Password berhasil diubah',
            ]
            );
    }

    public function postFCMToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
            'id_pengguna' => 'required|exists:penggunas,id_pengguna'
        ]);

        $pengguna = Pengguna::find($request->id_pengguna);
        $pengguna->fcm_token = $request->fcm_token;
        $pengguna->save();

        return response()->json([
            'status' => true,
            'message' => 'FCM token berhasil disimpan',
        ]);
    }
}
