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

                                        @foreach ($pembimbing as $item)
                                            <tr>

                                                <td>{{ $loop->iteration }}</td>

                                                <td>
                                                    Kelompok {{ $item->kelompok->nomor_kelompok ?? '-' }}
                                                </td>

                                                <td>
                                                    @if ($item->role_id == 1)
                                                        {{ $item->nama }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>

                                                <td>
                                                    @if ($item->role_id == 2)
                                                        {{ $item->nama }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>

                                                <td>
                                                    <div class="d-flex">

                                                        <a href="{{ route('pembimbing.edit', Crypt::encrypt($item->id)) }}"
                                                            class="btn btn-success btn-sm">

                                                            <i class="fas fa-edit"></i> Setting

                                                        </a>

                                                    </div>
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
