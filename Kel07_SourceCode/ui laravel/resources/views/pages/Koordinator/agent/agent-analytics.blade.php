@extends('layouts.main')
@section('title', 'Agent Analytics Dashboard')

@section('content')
<section class="section">
    <div class="section-header">
        <div class="section-header-back">
            <a href="{{ route('dashboard.koordinator') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
        </div>
        <h1>📊 Agent Analytics Dashboard</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('dashboard.koordinator') }}">Dashboard</a></div>
            <div class="breadcrumb-item">Agent Analytics</div>
        </div>
    </div>

    <div class="section-body">
        {{-- Status Alert --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <strong>Error!</strong> {{ $errors->first() }}
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                {{ session('success') }}
            </div>
        @endif

        {{-- MongoDB Connection Status --}}
        <div class="row mb-4">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4>🔌 MongoDB Connection Status</h4>
                    </div>
                    <div class="card-body">
                        @php
                            $isConnected = isset($mongoStatus) && ($mongoStatus['connected'] ?? false);
                        @endphp
                        @if ($isConnected)
                            <div class="alert alert-success" role="alert">
                                <i class="fas fa-check-circle"></i> MongoDB Connected
                                <span class="ml-2 text-muted">Service: {{ $mongoStatus['service'] ?? 'mongodb-memory' }}</span>
                                <br>
                                <small class="text-muted">Database: {{ $mongoStatus['database'] ?? 'VokasiTeraDB' }}</small>
                            </div>
                        @else
                            <div class="alert alert-danger" role="alert">
                                <i class="fas fa-times-circle"></i> MongoDB Disconnected
                                <span class="ml-2 text-muted">{{ $mongoStatus['error'] ?? 'Connection failed' }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Key Statistics --}}
        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-primary">
                        <i class="fas fa-comments"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Total Conversations</h4>
                        </div>
                        <div class="card-body">
                            {{ $statistics['total_conversations'] }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-success">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Actions Executed</h4>
                        </div>
                        <div class="card-body">
                            {{ $statistics['total_actions'] }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-warning">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Avg Response Time</h4>
                        </div>
                        <div class="card-body">
                            {{ number_format($statistics['avg_response_time'], 2) }}ms
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-info">
                        <i class="fas fa-check"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Success Rate</h4>
                        </div>
                        <div class="card-body">
                            {{ $statistics['success_rate'] }}%
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Performance Metrics --}}
        <div class="row mt-4">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4>📈 Response Time Metrics (Last 7 Days)</h4>
                    </div>
                    <div class="card-body">
                        @if ($metrics && ($metrics['count'] ?? 0) > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>Min Response Time</strong></td>
                                        <td>{{ number_format($metrics['min'] ?? 0, 2) }}ms</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Max Response Time</strong></td>
                                        <td>{{ number_format($metrics['max'] ?? 0, 2) }}ms</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Avg Response Time</strong></td>
                                        <td>{{ number_format($metrics['avg_response_time'] ?? 0, 2) }}ms</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total Requests</strong></td>
                                        <td>{{ $metrics['count'] ?? 0 }}</td>
                                    </tr>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">Belum ada data metrics</div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4>📊 User Analytics Summary</h4>
                    </div>
                    <div class="card-body">
                        @if ($userAnalytics)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>Total Messages</strong></td>
                                        <td>{{ $userAnalytics['total_messages'] ?? 0 }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total Actions</strong></td>
                                        <td>{{ $userAnalytics['total_actions'] ?? 0 }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Avg Response Time</strong></td>
                                        <td>{{ number_format($userAnalytics['avg_response_time_ms'] ?? 0, 2) }}ms</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Avg Quality Score</strong></td>
                                        <td>{{ number_format($userAnalytics['avg_quality_score'] ?? 0, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Last Activity</strong></td>
                                        <td>
                                            @if ($userAnalytics && ($userAnalytics['last_activity'] ?? null))
                                                {{ \Carbon\Carbon::parse($userAnalytics['last_activity'])->diffForHumans() }}
                                            @else
                                                Never
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>User ID</strong></td>
                                        <td>{{ $userAnalytics['user_id'] ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">Belum ada data analitik pengguna</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Conversations --}}
        <div class="row mt-4">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4>💬 Recent Conversations (Last 20)</h4>
                        <a href="{{ route('agent.analytics.refresh') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-sync"></i> Refresh
                        </a>
                    </div>
                    <div class="card-body">
                                        @if ($conversationHistory && ($conversationHistory['total'] ?? 0) > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th width="15%">Time</th>
                                            <th width="10%">Role</th>
                                            <th width="60%">Message</th>
                                            <th width="15%">Timestamp</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($conversationHistory['messages'] as $msg)
                                            <tr>
                                                <td>
                                                    <span class="badge {{ ($msg['role'] ?? 'user') === 'user' ? 'badge-primary' : 'badge-success' }}">
                                                        {{ ucfirst($msg['role'] ?? 'user') }}
                                                    </span>
                                                </td>
                                                <td>{{ ucfirst($msg['role'] ?? 'user') }}</td>
                                                <td>
                                                    <small>
                                                        {{ Str::limit($msg['content'] ?? '', 100) }}
                                                    </small>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        {{ \Carbon\Carbon::parse($msg['timestamp'] ?? now())->diffForHumans() }}
                                                    </small>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">Belum ada riwayat percakapan</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Execution Logs --}}
        <div class="row mt-4">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4>⚙️ Recent Action Execution Logs (Last 20)</h4>
                    </div>
                    <div class="card-body">
                        @if ($executionLogs && ($executionLogs['total'] ?? 0) > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th width="15%">Action Type</th>
                                            <th width="15%">Tool</th>
                                            <th width="15%">Status</th>
                                            <th width="40%">Result</th>
                                            <th width="15%">Timestamp</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($executionLogs['logs'] as $log)
                                            <tr>
                                                <td>
                                                    <code>{{ $log['action_type'] ?? '-' }}</code>
                                                </td>
                                                <td>
                                                    <small>{{ $log['tool_name'] ?? '-' }}</small>
                                                </td>
                                                <td>
                                                    <span class="badge {{ ($log['status'] ?? 'unknown') === 'success' ? 'badge-success' : 'badge-danger' }}">
                                                        {{ ucfirst($log['status'] ?? 'unknown') }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <small>{{ Str::limit(json_encode($log['result'] ?? []), 80) }}</small>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        {{ \Carbon\Carbon::parse($log['timestamp'] ?? now())->diffForHumans() }}
                                                    </small>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">Belum ada log eksekusi aksi</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- System Information --}}
        <div class="row mt-4">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4>ℹ️ System Information</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <p><strong>MongoDB Database:</strong> VokasiTeraDB</p>
                                <p><strong>Agent API URL:</strong> http://localhost:8002</p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Collections:</strong></p>
                                <ul class="small">
                                    <li>✓ sessions</li>
                                    <li>✓ messages</li>
                                    <li>✓ planner_logs</li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Collections (cont'd):</strong></p>
                                <ul class="small">
                                    <li>✓ executor_logs</li>
                                    <li>✓ metrics</li>
                                    <li>✓ memory_store</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

@push('style')
<style>
    .card-statistic-1 .card-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }

    .table-responsive table {
        margin-bottom: 0;
    }

    .alert {
        margin-bottom: 1rem;
    }

    .badge {
        padding: 0.35rem 0.65rem;
    }

    code {
        background-color: #f4f4f4;
        padding: 2px 6px;
        border-radius: 3px;
        color: #d63384;
        font-size: 12px;
    }
</style>
@endpush

@push('script')
<script>
    // Auto-refresh every 30 seconds (optional)
    // setInterval(() => {
    //     location.reload();
    // }, 30000);
</script>
@endpush

@endsection
