<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mahasiswa;
use App\Models\Kelas;
use App\Models\Mahasiswa_MataKuliah;
use Illuminate\Support\Facades\Storage;
use PDF;

class MahasiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //fungsi eloquent menampilkan data menggunakan pagination

        $mahasiswa = Mahasiswa::with('kelas')->orderBy('id_mahasiswa', 'asc')->paginate(4);
        return view('mahasiswa.index', compact('mahasiswa'));
    }

    public function page()
    {
        //fungsi eloquent menampilkan data menggunakan pagination
        $mahasiswa = Mahasiswa::with('kelas')->orderBy('id_mahasiswa', 'asc')->paginate(4);
        return view('mahasiswa.pagination', compact('mahasiswa'));
    }

    public function search(Request $request)
    {
        //fungsi eloquent menampilkan data menggunakan pagination
        $keyword = $request->search;
        $mahasiswa = Mahasiswa::where('nim', 'LIKE','%'.$keyword.'%')
                                ->orWhere('nama', 'LIKE','%'.$keyword.'%')
                                ->orWhere('email', 'LIKE','%'.$keyword.'%')
                                ->paginate(4);
        return view('mahasiswa.pagination', compact('mahasiswa'))->with('i', (request()->input('page', 1) - 1) * 4);
        // return view('users.index', compact('users'))->with('i', (request()->input('page', 1) - 1) * 5);
    }

    public function create()
    {
        $kelas = Kelas::all();
        return view('mahasiswa.create', compact('kelas'));
    }
    
    public function store(Request $request)
    {
        //melakukan validasi data
        $request->validate([
            'Nim' => 'required',
            'Email' => 'required',
            'Nama' => 'required',
            'Kelas' => 'required',
            'Jurusan' => 'required',
            'Alamat' => 'required',
            'Lahir' => 'required',
            'userfile' => 'required'
        ]);
        
        if($request->file('userfile')){
            $image_name = $request->file('userfile')->store('image', 'public');
        }
        // dd($request->all());
        $mahasiswa = new Mahasiswa;
        $mahasiswa->nim = $request->get('Nim');
        $mahasiswa->email = $request->get('Email');
        $mahasiswa->nama = $request->get('Nama');
        $mahasiswa->jurusan = $request->get('Jurusan');
        $mahasiswa->photo_profile = $image_name;
        $mahasiswa->alamat = $request->get('Alamat');
        $mahasiswa->tanggal_lahir = $request->get('Lahir');
        $mahasiswa->save();

        $kelas = new Kelas;
        $kelas->id = $request->get('Kelas');

        $mahasiswa->kelas()->associate($kelas);
        $mahasiswa->save();

        //jika data berhasil ditambahkan, akan kembali ke halaman utama
        return redirect()->route('mahasiswa.index')
            ->with('success', 'Mahasiswa Berhasil Ditambahkan');
    }

    public function show($nim)
    {
        //menampilkan detail data dengan menemukan/berdasarkan Nim Mahasiswa
        $Mahasiswa = Mahasiswa::with('kelas')->where('nim', $nim)->first();
        return view('mahasiswa.detail', compact('Mahasiswa'));
    }

    public function edit($nim)
    {
        //menampilkan detail data dengan menemukan berdasarkan Nim Mahasiswa untuk diedit
        $kelas = Kelas::all();
        $Mahasiswa = Mahasiswa::with('kelas')->where('nim', $nim)->first();
        return view('mahasiswa.edit', compact('Mahasiswa', 'kelas'));
    }

    public function update(Request $request, $nim)
    {
        //melakukan validasi data
        $request->validate([
            'Nim' => 'required',
            'Email' => 'required',
            'Nama' => 'required',
            'Kelas' => 'required',
            'Jurusan' => 'required',
            'Alamat' => 'required',
            'Lahir' => 'required',
            'userfile' => 'required'
        ]);

        $mahasiswa = Mahasiswa::with('kelas')->where('nim', $nim)->first();
        $mahasiswa->nim = $request->get('Nim');
        $mahasiswa->email = $request->get('Email');
        $mahasiswa->nama = $request->get('Nama');
        $mahasiswa->jurusan = $request->get('Jurusan');
        
        if($mahasiswa->photo_profile && file_exists(storage_path('./app/public/'. $mahasiswa->photo_profile))){
            Storage::delete(['./public/', $mahasiswa->photo_profile]);
        }
        
        $image_name = $request->file('userfile')->store('image', 'public');
        $mahasiswa->photo_profile = $image_name;

        $mahasiswa->alamat = $request->get('Alamat');
        $mahasiswa->tanggal_lahir = $request->get('Lahir');
        $mahasiswa->save();

        $kelas = new Kelas;
        $kelas->id = $request->get('Kelas');

        $mahasiswa->kelas()->associate($kelas);
        $mahasiswa->save();
        
        //jika data berhasil diupdate, akan kembali ke halaman utama
        return redirect()->route('mahasiswa.index')
            ->with('success', 'Mahasiswa Berhasil Diupdate');
    }

    public function destroy( $nim)
    {
        //fungsi eloquent untuk menghapus data
        Mahasiswa::where('nim', $nim)->delete();
        return redirect()->route('mahasiswa.index')
            ->with('success', 'Mahasiswa Berhasil Dihapus');
    } 

    public function khs($nim){
        $mhs = Mahasiswa::where('nim', $nim)->first();
        $nilai = Mahasiswa_MataKuliah::where('mahasiswa_id', $mhs->id_mahasiswa)
                                       ->with('matakuliah')
                                       ->with('mahasiswa')
                                       ->get();
        $nilai->mahasiswa = Mahasiswa::with('kelas')->where('nim', $nim)->first();
        // dd($nilai);
        
        return view('mahasiswa.khs', compact('nilai'));
    }

    public function cetak_pdf($nim){
        // dd('tetsing');
        $mhs = Mahasiswa::where('nim', $nim)->first();
        $nilai = Mahasiswa_MataKuliah::where('mahasiswa_id', $mhs->id_mahasiswa)
                                       ->with('matakuliah')
                                       ->with('mahasiswa')
                                       ->get();
        $nilai->mahasiswa = Mahasiswa::with('kelas')->where('nim', $nim)->first();
        $pdf = PDF::loadview('mahasiswa.nilai_pdf', compact('nilai'));
        return $pdf->stream();
    }
}
