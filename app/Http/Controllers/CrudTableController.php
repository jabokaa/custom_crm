<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCrudTableRequest;
use App\Services\CrudTableService;
use App\Models\CrudTable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CrudTableController extends Controller
{
    public function __construct(
        protected CrudTableService $service,
    ) {}

    public function index(): View
    {
        $crudTables = CrudTable::withCount('columns')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('crud_tables.index', compact('crudTables'));
    }

    public function create(): View
    {
        return view('crud_tables.create');
    }

    public function store(StoreCrudTableRequest $request): RedirectResponse
    {
        $this->service->createWithColumns($request->validated());

        return redirect()->route('crud_tables.index')
            ->with('success', 'Tabela de CRUD criada com sucesso.');
    }

    public function edit(CrudTable $crudTable): View
    {
        $crudTable->load('columns');

        return view('crud_tables.edit', compact('crudTable'));
    }

    public function update(StoreCrudTableRequest $request, CrudTable $crudTable): RedirectResponse
    {
        $this->service->updateWithColumns($crudTable, $request->validated());

        return redirect()->route('crud_tables.index')
            ->with('success', 'Tabela de CRUD atualizada com sucesso.');
    }

    public function listRecords(string $slug): View
    {
        $crudTable = CrudTable::with('columns')
            ->where('slug', $slug)
            ->firstOrFail();

        $records = $this->service->listRecords($crudTable);

        return view('crud_records.index', compact('crudTable', 'records'));
    }

    public function createRecord(string $slug): View
    {
        $crudTable = CrudTable::with('columns')
            ->where('slug', $slug)
            ->firstOrFail();

        return view('crud_records.create', compact('crudTable'));
    }

    public function storeRecord(Request $request, string $slug): RedirectResponse
    {
        $crudTable = CrudTable::with('columns')
            ->where('slug', $slug)
            ->firstOrFail();

        $rules = [];

        foreach ($crudTable->columns as $column) {
            $fieldName = $column->column_name;
            $fieldRules = [];

            $fieldRules[] = $column->is_required ? 'required' : 'nullable';

            switch ($column->field_type) {
                case 'text':
                    $fieldRules[] = 'string';
                    break;
                case 'select':
                    $fieldRules[] = 'string';
                    break;
                case 'image':
                    // por enquanto, trata como string (caminho/URL); pode ser ajustado depois para upload
                    $fieldRules[] = 'string';
                    break;
                default:
                    $fieldRules[] = 'string';
            }

            $rules[$fieldName] = implode('|', $fieldRules);
        }

        $validated = $request->validate($rules);

        $this->service->createRecord($crudTable, $validated);

        return redirect()->route('crud_records.index', $crudTable->slug)
            ->with('success', 'Registro criado com sucesso.');
    }
}
