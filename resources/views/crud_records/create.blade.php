<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Criar registro - {{ $crudTable->name }}</title>

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
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8 col-xl-7">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h1 class="h3 mb-0">Criar registro</h1>
                        <small class="text-muted">Tabela: {{ $crudTable->name }} ({{ $crudTable->slug }})</small>
                    </div>
                    <a href="{{ route('crud_tables.edit', $crudTable) }}" class="btn btn-outline-secondary btn-sm">Configurar campos</a>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <h6 class="alert-heading">Erros de validação</h6>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card shadow-sm">
                    <div class="card-body">
                        <form method="POST" action="{{ route('crud_records.store', $crudTable->slug) }}">
                            @csrf

                            @foreach ($crudTable->columns->sortBy('position') as $column)
                                <div class="mb-3">
                                    <label class="form-label">{{ $column->label }}</label>

                                    @php
                                        $name = $column->column_name;
                                        $value = old($name);
                                    @endphp

                                    @if ($column->field_type === 'text')
                                        <input
                                            type="text"
                                            name="{{ $name }}"
                                            class="form-control {{ $column->css_class }}"
                                            value="{{ $value }}"
                                            {{ $column->is_required ? 'required' : '' }}
                                        >
                                    @elseif ($column->field_type === 'select' && is_array($column->options))
                                        <select
                                            name="{{ $name }}"
                                            class="form-select {{ $column->css_class }}"
                                            {{ $column->is_required ? 'required' : '' }}
                                        >
                                            <option value="">Selecione...</option>
                                            @foreach ($column->options as $option)
                                                <option value="{{ $option['value'] ?? $option['label'] ?? '' }}" @selected($value == ($option['value'] ?? $option['label'] ?? ''))>
                                                    {{ $option['label'] ?? $option['value'] ?? '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @elseif ($column->field_type === 'image')
                                        <input
                                            type="text"
                                            name="{{ $name }}"
                                            class="form-control {{ $column->css_class }}"
                                            value="{{ $value }}"
                                            placeholder="URL ou caminho da imagem"
                                            {{ $column->is_required ? 'required' : '' }}
                                        >
                                    @else
                                        <input
                                            type="text"
                                            name="{{ $name }}"
                                            class="form-control {{ $column->css_class }}"
                                            value="{{ $value }}"
                                            {{ $column->is_required ? 'required' : '' }}
                                        >
                                    @endif

                                    @if ($column->description)
                                        <div class="form-text">{{ $column->description }}</div>
                                    @endif
                                </div>
                            @endforeach

                            <div class="text-end">
                                <button type="submit" class="btn btn-success">
                                    Salvar registro
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"
    ></script>
</body>
</html>
