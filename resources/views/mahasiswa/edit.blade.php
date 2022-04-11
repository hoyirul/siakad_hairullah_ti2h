@extends('mahasiswa.layout')
@section('content')
<div class="container mt-5">
  <div class="row justify-content-center align-items-center">
    <div class="card" style="width: 24rem;">
      <div class="card-header">
        Edit Mahasiswa
      </div>
      <div class="card-body">
        @if ($errors->any())
        <div class="alert alert-danger">
          <strong>Whoops!</strong> There were some problems with your input.<br><br>
          <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
        @endif
        <form method="post" action="{{ route('mahasiswa.update', $Mahasiswa->nim) }}" id="myForm">
          @csrf
          @method('PUT')
          <div class="form-group">
            <label for="Nim">Nim</label>
            <input type="text" name="Nim" class="form-control" id="Nim" value="{{ $Mahasiswa->nim }}" aria-describedby="Nim" >
          </div>
          <div class="form-group">
            <label for="Email">Email</label>
            <input type="text" name="Email" class="form-control" value="{{ $Mahasiswa->email }}" id="Email" aria-describedby="Email" >
          </div>
          <div class="form-group">
            <label for="Nama">Nama</label>
            <input type="text" name="Nama" class="form-control" id="Nama" value="{{ $Mahasiswa->nama }}" aria-describedby="Nama" >
          </div>
          <div class="form-group">
            <label for="kelas">Pilih Kelas</label>
            <select name="Kelas" class="form-control" id="kelas">
              @foreach ($kelas as $item)
                <option value="{{ $item->id }}" {{ ($Mahasiswa->kelas_id == $item->id) ? 'selected' : '' }}>{{ $item->nama_kelas }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label for="Jurusan">Jurusan</label> 
            <input type="Jurusan" name="Jurusan" class="form-control" id="Jurusan" value="{{ $Mahasiswa->jurusan }}" aria-describedby="Jurusan" >
          </div>
          <div class="form-group">
            <label for="Alamat">Alamat</label>
            <textarea name="Alamat" class="form-control">{{ $Mahasiswa->alamat }}</textarea>
          </div>
          <div class="form-group">
            <label for="Lahir">Lahir</label>
            <input type="date" name="Lahir" class="form-control" id="Lahir" ariadescribedby="Lahir" value="{{ $Mahasiswa->tanggal_lahir }}">
          </div>
          <button type="submit" class="btn btn-primary">Submit</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection