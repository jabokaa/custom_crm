<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Registros - {{ $crudTable->name }}</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous"
    >

    <style>
        body {
            background-color: #f5f5f7;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Custom CRM</a>
        </div>
    </nav>

    <main class="container mb-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h1 class="h3 mb-0">Registros</h1>
                <small class="text-muted">Tabela: {{ $crudTable->name }} ({{ $crudTable->slug }})</small>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('crud_records.create', $crudTable->slug) }}" class="btn btn-primary btn-sm">+ Novo registro</a>
                <a href="{{ route('crud_tables.edit', $crudTable) }}" class="btn btn-outline-secondary btn-sm">Configurar campos</a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($records->isEmpty())
            <div class="alert alert-info">
                Nenhum registro encontrado para esta tabela ainda.
            </div>
        @else
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    @foreach ($crudTable->columns->where('is_visible', true)->sortBy('position') as $column)
                                        <th>{{ $column->label }}</th>
                                    @endforeach
                                    <th>Criado em</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($records as $record)
                                    <tr>
                                        <td>{{ $record->id }}</td>
                                        @foreach ($crudTable->columns->where('is_visible', true)->sortBy('position') as $column)
                                            <td>
                                                @php
                                                    $value = $record->{$column->column_name} ?? null;
                                                @endphp

                                                @if ($column->field_type === 'image' && $value)
                                                    <img src="{{ $value }}" alt="" style="max-height:40px; max-width:80px; object-fit:cover;">
                                                @else
                                                    {{ $value }}
                                                @endif
                                            </td>
                                        @endforeach
                                        <td>{{ $record->created_at ?? '' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                @if ($records->hasPages())
                    <div class="card-footer">
                        {{ $records->links() }}
                    </div>
                @endif
            </div>
        @endif
    </main>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"
    ></script>
</body>
</html>
