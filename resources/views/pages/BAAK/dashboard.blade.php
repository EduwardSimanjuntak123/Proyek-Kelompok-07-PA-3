@extends('layouts.main')
@section('title', 'Dashboard')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Dashboard BAAK</h1>
        </div>

         <div class="container">
                <h2 class="text-center mb-4">Kalender Jadwal Seminar</h2>
                <div class="card shadow">
                    <div class="card-body">
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </section>
@endsection
@push('script')
    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var calendarEl = document.getElementById('calendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                themeSystem: 'bootstrap5',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: ''
                },
                events: @json($events), // events: array of { title, start, end }
                eventDisplay: 'block', // tampilkan seluruh title
            });

            calendar.render();
        });
    </script>
@endpush