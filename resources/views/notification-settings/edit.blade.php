@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Editar Configuração de Notificação</h3>
                </div>
                <form action="{{ route('notification-settings.update', $notificationSetting) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="event_type">Tipo de Evento *</label>
                                    <select class="form-control @error('event_type') is-invalid @enderror" id="event_type" name="event_type" required>
                                        <option value="">Selecione um evento</option>
                                        @foreach($eventTypes as $key => $name)
                                            <option value="{{ $key }}" {{ old('event_type', $notificationSetting->event_type) == $key ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('event_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="notification_type">Tipo de Notificação *</label>
                                    <select class="form-control @error('notification_type') is-invalid @enderror" id="notification_type" name="notification_type" required>
                                        <option value="">Selecione o tipo</option>
                                        <option value="database" {{ old('notification_type', $notificationSetting->notification_type) == 'database' ? 'selected' : '' }}>Banco de Dados</option>
                                        <option value="mail" {{ old('notification_type', $notificationSetting->notification_type) == 'mail' ? 'selected' : '' }}>Email</option>
                                        <option value="both" {{ old('notification_type', $notificationSetting->notification_type) == 'both' ? 'selected' : '' }}>Ambos</option>
                                    </select>
                                    @error('notification_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="project_id">Projeto</label>
                                    <select class="form-control @error('project_id') is-invalid @enderror" id="project_id" name="project_id">
                                        <option value="">Selecione um projeto (opcional)</option>
                                        @foreach($projects as $project)
                                            <option value="{{ $project->id }}" {{ old('project_id', $notificationSetting->project_id) == $project->id ? 'selected' : '' }}>
                                                {{ $project->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('project_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="institution_id">Instituição</label>
                                    <select class="form-control @error('institution_id') is-invalid @enderror" id="institution_id" name="institution_id">
                                        <option value="">Selecione uma instituição (opcional)</option>
                                        @foreach($institutions as $institution)
                                            <option value="{{ $institution->id }}" {{ old('institution_id', $notificationSetting->institution_id) == $institution->id ? 'selected' : '' }}>
                                                {{ $institution->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('institution_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Destinatários</label>
                            <div class="row">
                                @php
                                    $selectedRecipients = old('recipients', $notificationSetting->recipients ?? []);
                                    if (is_string($selectedRecipients)) {
                                        $selectedRecipients = json_decode($selectedRecipients, true) ?? [];
                                    }
                                @endphp
                                @foreach($roles as $role)
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="role_{{ $role->name }}" name="recipients[]" value="role:{{ $role->name }}" {{ in_array('role:'.$role->name, $selectedRecipients) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="role_{{ $role->name }}">
                                            {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @error('recipients')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="enabled" name="enabled" value="1" {{ old('enabled', $notificationSetting->enabled) ? 'checked' : '' }}>
                            <label class="form-check-label" for="enabled">
                                Habilitado
                            </label>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Salvar</button>
                        <a href="{{ route('admin.notification-settings.index') }}" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
