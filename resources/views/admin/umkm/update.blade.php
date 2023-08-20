@extends('admin.layouts.master')

@section('page_title')
UMKM  | UMKM2M Kecamatan Siantar Marimbun
@endsection

@section('breadcrumb')
    <div class="page-header">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">UMKM</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Data UMKM </li>
            </ol>
        </nav>
    </div>
@endsection

@section('content')

    <div class="card shadow">
        <div class="card-header border-0">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="mb-0 text-center">Edit Data UMKM</h1>
                    <hr width=50%>
                </div>
            </div>
        </div>
        <div class="card-body border-0">
            <form action="{{route('user.update',$users -> user_id)}}" method="POST">
                @csrf
                @method('PATCH')

                <div class="form-group row">
                    <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Nama UMKM') }}</label>
                    <div class="col-md-6">
                        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{$users -> name}}" required autocomplete="name" autofocus>
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group row">
                    <label for="address" class="col-md-4 col-form-label text-md-right">{{ __('Alamat') }}</label>
                    <div class="col-md-6">
                        <input id="address" type="text" class="form-control @error('address') is-invalid @enderror" name="address" value="{{$users -> address}}" required autocomplete="address" autofocus>
                        @error('address')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group row">
                    <label for="username" class="col-md-4 col-form-label text-md-right">{{ __('Username') }}</label>
                    <div class="col-md-6">
                        <input id="username" type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{$users -> username}}" required autocomplete="username" autofocus>
                        @error('username')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group row mb-0">
                    <div class="col-md-12 offset-md-12 text-center">
                        <button type="submit" class="btn btn-warning" onclick="return confirm('Apakah Data Sudah Benar ?')">
                            {{ __('Update') }}
                        </button>
                        <button type="reset" class="btn btn-light">
                            {{ __('Reset') }}
                        </button>
                        <a href="{!! url('user') !!}" class="btn btn-danger">Batal</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
