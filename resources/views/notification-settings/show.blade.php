@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Detalhes da Configuração de Notificação</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.notification-settings.edit', $notificationSetting) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <a href="{{ route('admin.notification-settings.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Tipo de Evento:</strong></label>
                                <p>{{ $notificationSetting->getEventTypeLabel() }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Tipo de Notificação:</strong></label>
                                <p>
                                    @if($notificationSetting->notification_type === 'database')
                                        <span class="badge badge-info">Banco de Dados</span>
                                    @elseif($notificationSetting->notification_type === 'mail')
                                        <span class="badge badge-primary">Email</span>
                                    @else
                                        <span class="badge badge-success">Ambos</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Projeto:</strong></label>
                                <p>{{ $notificationSetting->project ? $notificationSetting->project->name : 'Global (todos os projetos)' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Instituição:</strong></label>
                                <p>{{ $notificationSetting->institution ? $notificationSetting->institution->name : 'Global (todas as instituições)' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label><strong>Destinatários:</strong></label>
                        <div class="row">
                            @php
                                $recipients = is_array($notificationSetting->recipients) ? $notificationSetting->recipients : json_decode($notificationSetting->recipients, true) ?? [];
                            @endphp
                            @foreach($recipients as $recipient)
                                @php
                                    $roleName = str_replace('role:', '', $recipient);
                                    $role = \Spatie\Permission\Models\Role::where('name', $roleName)->first();
                                @endphp
                                @if($role)
                                    <div class="col-md-3">
                                        <span class="badge badge-secondary">{{ ucfirst(str_replace('_', ' ', $role->name)) }}</span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <div class="form-group">
                        <label><strong>Status:</strong></label>
                        <p>
                            @if($notificationSetting->enabled)
                                <span class="badge badge-success">Habilitado</span>
                            @else
                                <span class="badge badge-danger">Desabilitado</span>
                            @endif
                        </p>
                    </div>

                    <div class="form-group">
                        <label><strong>Criado em:</strong></label>
                        <p>{{ $notificationSetting->created_at->format('d/m/Y H:i') }}</p>
                    </div>

                    <div class="form-group">
                        <label><strong>Última atualização:</strong></label>
                        <p>{{ $notificationSetting->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection