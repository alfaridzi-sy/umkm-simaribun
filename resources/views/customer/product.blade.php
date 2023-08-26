@extends('customer.layouts.master')

@section('page_title')
    Produk | UMKM Be Digital Sidamanik
@endsection

@section('page_type')
    <div class="hero_area sub_pages">
@endsection

@section('content')
<section class="fruit_section mt-5">
    <div class="container">
        <h2 class="custom_heading">Daftar Produk</h2>
        <p class="custom_heading-text">
            Banyak produk menarik yang merupakan produk murni dari UMKM setempat
        </p>
        @foreach ($products as $product)
            <div class="row layout_padding2">
                <div class="col-md-8">
                    <div class="fruit_detail-box">
                        <h3>{{ $product -> nama_produk }}</h3>
                        <button type="button" class="btn btn-sm btn-secondary" style="border-radius: 12px;" disabled>Rp. <?php echo number_format($product -> harga, 0, '.', '.') ?></button>
                        <button type="button" class="btn btn-sm btn-secondary" style="border-radius: 12px;"disabled>{{ $product -> category -> jenis_produk }}</button>
                        <button type="button" class="btn btn-sm btn-secondary" style="border-radius: 12px;"disabled>{{ $product -> stok }} Tersisa</button>
                        <button type="button" class="btn btn-sm btn-secondary" style="border-radius: 12px;"disabled>{{ $product -> cluster -> nama }}</button>
                        <p class="mt-4 mb-5">
                            {{ $product -> deskripsi_produk }}
                        </p>
                        <div>
                            <a href="#" class="custom_dark-btn">
                                Beli Sekarang
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 d-flex justify-content-center align-items-center">
                    <div class="fruit_img-box d-flex justify-content-center align-items-center">
                        <img src="https://sdmnk.umkmbedigital.com/public/storage/product/{{ $product -> foto }}" alt="" class="" width="250px" />
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>
@endsection
