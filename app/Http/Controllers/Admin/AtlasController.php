<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Capability;
use App\Models\CompanyType;
use App\Models\Department;
use App\Models\FrictionPoint;
use App\Models\Industry;
use App\Models\SolutionPattern;
use App\Models\Workflow;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AtlasController extends Controller
{
    private array $resources = [
        'industries' => ['model' => Industry::class, 'label' => 'Industries', 'singular' => 'Industry', 'children' => ['companyTypes']],
        'company-types' => ['model' => CompanyType::class, 'label' => 'Company Types', 'singular' => 'Company Type', 'parents' => ['industry_id' => Industry::class], 'children' => ['departments', 'workflows']],
        'departments' => ['model' => Department::class, 'label' => 'Departments', 'singular' => 'Department', 'parents' => ['company_type_id' => CompanyType::class], 'children' => ['workflows']],
        'workflows' => ['model' => Workflow::class, 'label' => 'Workflows', 'singular' => 'Workflow', 'parents' => ['department_id' => Department::class], 'children' => ['frictionPoints']],
        'friction-points' => ['model' => FrictionPoint::class, 'label' => 'Friction Points', 'singular' => 'Friction Point', 'parents' => ['workflow_id' => Workflow::class], 'many' => ['solution_pattern_ids' => SolutionPattern::class], 'children' => []],
        'solution-patterns' => ['model' => SolutionPattern::class, 'label' => 'Solution Patterns', 'singular' => 'Solution Pattern', 'many' => ['capability_ids' => Capability::class], 'children' => ['frictionPoints']],
        'capabilities' => ['model' => Capability::class, 'label' => 'Capabilities', 'singular' => 'Capability', 'children' => ['solutionPatterns']],
    ];

    public function index(string $resource): View
    {
        $config = $this->config($resource);
        $query = $config['model']::query()->latest();
        foreach ($this->relationNames($config) as $relation) {
            $query->with($relation);
        }

        return view('admin.atlas.index', $this->viewData($resource, ['items' => $query->paginate(20)]));
    }

    public function create(string $resource): View
    {
        $model = new ($this->config($resource)['model']);
        return view('admin.atlas.create', $this->viewData($resource, ['item' => $model]));
    }

    public function store(Request $request, string $resource): RedirectResponse
    {
        $config = $this->config($resource);
        $data = $this->validated($request, $resource);
        $many = $this->extractManyToMany($data);
        $data['slug'] = Str::slug($data['slug'] ?: $data['name']);
        $model = $config['model']::create($data);
        $this->syncManyToMany($model, $many);

        return redirect()->route('admin.atlas.edit', [$resource, $model])->with('status', $config['singular'].' created.');
    }

    public function edit(string $resource, int $id): View
    {
        $config = $this->config($resource);
        $item = $config['model']::with($this->relationNames($config))->findOrFail($id);
        return view('admin.atlas.edit', $this->viewData($resource, ['item' => $item]));
    }

    public function update(Request $request, string $resource, int $id): RedirectResponse
    {
        $config = $this->config($resource);
        $model = $config['model']::findOrFail($id);
        $data = $this->validated($request, $resource, $model);
        $many = $this->extractManyToMany($data);
        $data['slug'] = Str::slug($data['slug'] ?: $data['name']);
        $model->update($data);
        $this->syncManyToMany($model, $many);

        return back()->with('status', $config['singular'].' updated.');
    }

    public function destroy(string $resource, int $id): RedirectResponse
    {
        $config = $this->config($resource);
        $model = $config['model']::findOrFail($id);
        foreach ($config['children'] ?? [] as $relation) {
            if ($model->{$relation}()->exists()) {
                return back()->withErrors($config['singular'].' cannot be deleted while related records exist.');
            }
        }
        $model->delete();
        return redirect()->route('admin.atlas.index', $resource)->with('status', $config['singular'].' deleted.');
    }

    private function validated(Request $request, string $resource, ?Model $model = null): array
    {
        $config = $this->config($resource);
        $table = (new $config['model'])->getTable();
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique($table, 'slug')->ignore($model)],
            'description' => ['nullable', 'string'],
        ];
        if ($resource === 'friction-points') {
            $rules['impact'] = ['nullable', 'string', 'max:255'];
        }
        foreach ($config['parents'] ?? [] as $field => $class) {
            $rules[$field] = ['required', Rule::exists((new $class)->getTable(), 'id')];
        }
        foreach ($config['many'] ?? [] as $field => $class) {
            $rules[$field] = ['array'];
            $rules[$field.'.*'] = [Rule::exists((new $class)->getTable(), 'id')];
        }

        return $request->validate($rules);
    }

    private function viewData(string $resource, array $extra = []): array
    {
        return $extra + ['resource' => $resource, 'config' => $this->config($resource), 'resources' => $this->resources, 'options' => [
            'industries' => Industry::orderBy('name')->get(), 'companyTypes' => CompanyType::orderBy('name')->get(), 'departments' => Department::orderBy('name')->get(),
            'workflows' => Workflow::orderBy('name')->get(), 'solutionPatterns' => SolutionPattern::orderBy('name')->get(), 'capabilities' => Capability::orderBy('name')->get(),
        ]];
    }

    private function config(string $resource): array
    {
        abort_unless(isset($this->resources[$resource]), 404);
        return $this->resources[$resource];
    }

    private function relationNames(array $config): array
    {
        $names = [];
        foreach (array_keys($config['parents'] ?? []) as $field) $names[] = Str::camel(Str::beforeLast($field, '_id'));
        if (isset($config['many']['solution_pattern_ids'])) $names[] = 'solutionPatterns';
        if (isset($config['many']['capability_ids'])) $names[] = 'capabilities';
        return $names;
    }

    private function extractManyToMany(array &$data): array
    {
        $many = ['solution_pattern_ids' => $data['solution_pattern_ids'] ?? null, 'capability_ids' => $data['capability_ids'] ?? null];
        unset($data['solution_pattern_ids'], $data['capability_ids']);
        return array_filter($many, fn ($value) => $value !== null);
    }

    private function syncManyToMany(Model $model, array $many): void
    {
        if (isset($many['solution_pattern_ids']) && method_exists($model, 'solutionPatterns')) $model->solutionPatterns()->sync($many['solution_pattern_ids']);
        if (isset($many['capability_ids']) && method_exists($model, 'capabilities')) $model->capabilities()->sync($many['capability_ids']);
    }
}
