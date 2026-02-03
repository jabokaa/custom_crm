<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Criar Tabela de CRUD</title>

    <!-- Bootstrap 5 -->
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

        .page-title {
            font-weight: 600;
        }

        .column-card {
            border-radius: .75rem;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const addColumnButton = document.getElementById('add-column');
            const columnsContainer = document.getElementById('columns-container');

            function createColumnRow(index) {
                const wrapper = document.createElement('div');
                wrapper.classList.add('column-row', 'mb-3');

                wrapper.innerHTML = `
                    <div class="card shadow-sm column-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Coluna #${index + 1}</h6>
                                <button type="button" class="btn btn-sm btn-outline-danger remove-column">
                                    Remover
                                </button>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Nome da coluna</label>
                                    <input type="text" class="form-control" name="columns[${index}][column_name]" required>
                                    <div class="form-text">Nome do campo na base (ex: <code>first_name</code>).</div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Rótulo</label>
                                    <input type="text" class="form-control" name="columns[${index}][label]" required>
                                    <div class="form-text">Texto exibido na tela (ex: "Nome").</div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Tipo</label>
                                    <select class="form-select" name="columns[${index}][field_type]" required>
                                        <option value="text">Texto</option>
                                        <option value="select">Select</option>
                                        <option value="image">Imagem</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row g-3 mt-2">
                                <div class="col-md-8">
                                    <label class="form-label">Descrição</label>
                                    <input type="text" class="form-control" name="columns[${index}][description]">
                                </div>

                                <div class="col-md-2 d-flex align-items-center">
                                    <div class="form-check mt-3 mt-md-4">
                                        <input class="form-check-input" type="checkbox" name="columns[${index}][is_required]" value="1" id="required-${index}">
                                        <label class="form-check-label" for="required-${index}">
                                            Obrigatório
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-2 d-flex align-items-center">
                                    <div class="form-check mt-3 mt-md-4">
                                        <input class="form-check-input" type="checkbox" name="columns[${index}][is_visible]" value="1" id="visible-${index}" checked>
                                        <label class="form-check-label" for="visible-${index}">
                                            Visível
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3 mt-2">
                                <div class="col-md-6">
                                    <label class="form-label">Options (JSON)</label>
                                    <input type="text" class="form-control" name="columns[${index}][options]" placeholder='[{"value":"1","label":"Opção 1"}]'>
                                    <div class="form-text">Usado para campos "select". Informe um array JSON de opções.</div>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">CSS class</label>
                                    <input type="text" class="form-control" name="columns[${index}][css_class]" placeholder="ex: text-muted">
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Ícone</label>
                                    <input type="text" class="form-control" name="columns[${index}][icon]" placeholder="ex: bi-person">
                                    <div class="form-text">Nome da classe do ícone (Bootstrap Icons, FontAwesome, etc.).</div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                wrapper.querySelector('.remove-column').addEventListener('click', () => {
                    wrapper.remove();
                });

                return wrapper;
            }

            let columnIndex = 0;

            addColumnButton.addEventListener('click', () => {
                columnsContainer.appendChild(createColumnRow(columnIndex++));
            });

            // adiciona uma linha inicial
            columnsContainer.appendChild(createColumnRow(columnIndex++));
        });
    </script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Custom CRM</a>
        </div>
    </nav>

    <main class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10 col-xl-9">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h1 class="h3 page-title mb-0">Criar Tabela de CRUD</h1>
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

                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <form method="POST" action="{{ route('crud_tables.store') }}">
                            @csrf

                            <h5 class="card-title mb-3">Informações da tabela</h5>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nome da tabela</label>
                                    <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                                    <div class="form-text">Nome amigável para identificar esta configuração de CRUD.</div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Slug (opcional)</label>
                                    <input type="text" class="form-control" name="slug" value="{{ old('slug') }}">
                                    <div class="form-text">Identificador usado em URLs ou código. Será gerado automaticamente se vazio.</div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Descrição</label>
                                <textarea class="form-control" name="description" rows="3">{{ old('description') }}</textarea>
                            </div>

                            <hr>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title mb-0">Colunas</h5>
                                <button type="button" id="add-column" class="btn btn-primary btn-sm">
                                    + Adicionar coluna
                                </button>
                            </div>

                            <div id="columns-container" class="mb-3"></div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-success">
                                    Salvar Tabela
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
