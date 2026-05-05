@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Configurações de Notificações</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.notification-settings.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Nova Configuração
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Evento</th>
                                    <th>Tipo</th>
                                    <th>Projeto</th>
                                    <th>Instituição</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($settings as $setting)
                                <tr>
                                    <td>{{ $setting->event_type }}</td>
                                    <td>{{ ucfirst($setting->notification_type) }}</td>
                                    <td>{{ $setting->project?->name ?? '-' }}</td>
                                    <td>{{ $setting->institution?->name ?? '-' }}</td>
                                    <td>
                                        @if($setting->enabled)
                                            <span class="badge bg-success">Ativo</span>
                                        @else
                                            <span class="badge bg-secondary">Inativo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.notification-settings.edit', $setting) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">Nenhuma configuração encontrada.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($settings->hasPages())
                <div class="card-footer">
                    {{ $settings->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection