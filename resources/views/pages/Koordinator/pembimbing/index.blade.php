@extends('layouts.main')
@section('title', 'Manajemen Pembimbing')

@section('content')
    <section class="section custom-section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">

                    <div class="card">

                        <div class="card-header">
                            <h4>Manajemen Pembimbing Kelompok</h4>
                        </div>

                        <div class="card-body">

                            @include('partials.alert')

                            <div class="table-responsive">

                                <table class="table table-striped">

                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nomor Kelompok</th>
                                            <th>Pembimbing 1</th>
                                            <th>Pembimbing 2</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        @foreach ($kelompok as $item)
                                            @php
                                                $pembimbing1 = $item->pembimbing->get(0);
                                                $pembimbing2 = $item->pembimbing->get(1);
                                            @endphp

                                            <tr>

                                                <td>{{ $loop->iteration }}</td>

                                                <td>
                                                    Kelompok {{ $item->nomor_kelompok ?? '-' }}
                                                </td>

                                                <td>
                                                    {{ $pembimbing1->nama ?? '-' }}
                                                </td>

                                                <td>
                                                    {{ $pembimbing2->nama ?? '-' }}
                                                </td>

                                                <td>

                                                    <a href="{{ route('pembimbing.create', Crypt::encrypt($item->id)) }}"
                                                        class="btn btn-success btn-sm">

                                                        <i class="fas fa-plus"></i> Setting Pembimbing

                                                    </a>

                                                    <a href="{{ route('pembimbing.edit', Crypt::encrypt($item->id)) }}"
                                                        class="btn btn-success btn-sm">

                                                        <i class="fas fa-edit"></i> Edit

                                                    </a>

                                                </td>

                                            </tr>
                                        @endforeach

                                    </tbody>

                                </table>

                            </div>

                        </div>

                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection
