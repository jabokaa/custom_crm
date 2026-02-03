<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <!DOCTYPE html>
        <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <meta name="csrf-token" content="{{ csrf_token() }}">

            <title>{{ config('app.name', 'Custom CRM') }}</title>

            <!-- Bootstrap CSS -->
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        </head>
        <body class="bg-light">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
            <div class="container-fluid">
                <a class="navbar-brand" href="{{ url('/') }}">Custom CRM</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="mainNavbar">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="{{ url('/') }}">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('crud_tables.index') }}">CRUDs</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('crud_tables.create') }}">Novo CRUD</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h1 class="h3 mb-0">Bem-vindo ao Custom CRM</h1>
                    <small class="text-muted">Crie e gerencie CRUDs dinâmicos.</small>
                </div>
                <a href="{{ route('crud_tables.create') }}" class="btn btn-primary">
                    + Criar novo CRUD
                </a>
            </div>

            @if(isset($crudTables) && $crudTables->count())
                <div class="card">
                    <div class="card-header">
                        CRUDs configurados
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Nome</th>
                                        <th>Slug</th>
                                        <th>Campos</th>
                                        <th class="text-end">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($crudTables as $table)
                                    <tr>
                                        <td>{{ $table->id }}</td>
                                        <td>{{ $table->name }}</td>
                                        <td><span class="badge bg-secondary">{{ $table->slug }}</span></td>
                                        <td>{{ $table->columns_count }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('crud_records.index', $table->slug) }}" class="btn btn-sm btn-outline-primary">
                                                Ver registros
                                            </a>
                                            <a href="{{ route('crud_records.create', $table->slug) }}" class="btn btn-sm btn-outline-success">
                                                Novo registro
                                            </a>
                                            <a href="{{ route('crud_tables.edit', $table) }}" class="btn btn-sm btn-outline-secondary">
                                                Configurar campos
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-info">
                    Ainda não há nenhum CRUD configurado. Clique em <strong>"Criar novo CRUD"</strong> para começar.
                </div>
            @endif
        </div>
        @if (Route::has('login'))
            <div class="h-14.5 hidden lg:block"></div>
        @endif
    </body>
</html>
