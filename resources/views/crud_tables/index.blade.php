<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Listar Tabelas de CRUD</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous"
    >
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Custom CRM</a>
        </div>
    </nav>

    <main class="container mb-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0">Tabelas de CRUD</h1>
            <a href="{{ route('crud_tables.create') }}" class="btn btn-primary">+ Nova tabela</a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($crudTables->isEmpty())
            <div class="alert alert-info">
                Nenhuma tabela de CRUD cadastrada ainda.
            </div>
        @else
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Nome</th>
                                    <th>Slug</th>
                                    <th>Qtd. colunas</th>
                                    <th>Criado em</th>
                                    <th class="text-end">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($crudTables as $crudTable)
                                    <tr>
                                        <td>{{ $crudTable->id }}</td>
                                        <td>{{ $crudTable->name }}</td>
                                        <td><span class="badge bg-secondary">{{ $crudTable->slug }}</span></td>
                                        <td>{{ $crudTable->columns_count }}</td>
                                        <td>{{ $crudTable->created_at?->format('d/m/Y H:i') }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('crud_tables.edit', $crudTable) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                @if ($crudTables->hasPages())
                    <div class="card-footer">
                        {{ $crudTables->links() }}
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
