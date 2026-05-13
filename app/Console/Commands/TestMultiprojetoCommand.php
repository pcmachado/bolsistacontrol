<?php

namespace App\Console\Commands;

use App\Models\AttendanceRecord;
use App\Models\AttendanceSubmission;
use App\Models\Project;
use App\Models\ScholarshipHolder;
use App\Models\User;
use App\Services\ScholarshipHolderService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class TestMultiprojetoCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:multiprojeto';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Valida a funcionalidade multiprojeto em MyAttendance e MySubmissions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 VALIDAÇÃO FUNCIONALIDADE MULTIPROJETO');
        $this->line('==========================================');
        $this->newLine();

        // 1. Verificar se existem bolsistas com múltiplos projetos
        $this->info('1️⃣ VERIFICANDO BOLSISTAS COM MÚLTIPLOS PROJETOS');
        $holdersWithMultipleProjects = ScholarshipHolder::query()
            ->withCount('projects')
            ->having('projects_count', '>', 1)
            ->get();

        $this->info('Bolsistas com múltiplos projetos: '.$holdersWithMultipleProjects->count());

        if ($holdersWithMultipleProjects->isEmpty()) {
            $this->warn('⚠️  Nenhum bolsista com múltiplos projetos encontrado.');
        }

        $this->newLine();

        // 2. Testar ScholarshipHolderService::attendanceContext()
        $this->info('2️⃣ TESTANDO SERVIÇO DE CONTEXTO MULTIPROJETO');
        $service = app(ScholarshipHolderService::class);

        $testUser = User::whereHas('scholarshipHolder.projects')->first();
        if ($testUser) {
            $this->line("Usuário de teste: {$testUser->name} (ID: {$testUser->id})");

            // Testar sem project_id
            $context = $service->attendanceContext($testUser);
            $this->info('✅ Contexto sem project_id: '.count($context['projects']).' projetos encontrados');
            $activeProjectName = isset($context['activeProject']) && $context['activeProject'] ? $context['activeProject']->name : 'Nenhum';
            $this->line("   Projeto ativo: {$activeProjectName}");

            // Testar com project_id específico
            if ($context['projects']->isNotEmpty()) {
                $firstProject = $context['projects']->first();
                $contextWithProject = $service->attendanceContext($testUser, $firstProject->id);
                $this->info("✅ Contexto com project_id {$firstProject->id}: Projeto ativo = {$contextWithProject['activeProject']->name}");
            }

            // Testar com project_id inválido
            try {
                $service->attendanceContext($testUser, 999999);
                $this->error('❌ Erro esperado não ocorreu com project_id inválido');
            } catch (\Exception $e) {
                $this->info('✅ Validação correta: '.$e->getMessage());
            }
        } else {
            $this->warn('⚠️  Nenhum usuário com projetos encontrado');
        }

        $this->newLine();

        // 3. Verificar rotas "my"
        $this->info('3️⃣ VERIFICANDO ROTAS \'MY\'');
        $myRoutes = [
            'attendance.my',
            'my-attendance.submissions.my',
            'my-attendance.submissions.store',
            'my-attendance.submissions.show',
        ];

        foreach ($myRoutes as $routeName) {
            try {
                $route = Route::getRoutes()->getByName($routeName);
                if ($route) {
                    $this->info("✅ Rota '{$routeName}' existe: {$route->uri()}");
                } else {
                    $this->error("❌ Rota '{$routeName}' não encontrada");
                }
            } catch (\Exception $e) {
                $this->error("❌ Erro ao verificar rota '{$routeName}': {$e->getMessage()}");
            }
        }

        $this->newLine();

        // 4. Verificar estrutura dos models
        $this->info('4️⃣ VERIFICANDO ESTRUTURA DOS MODELS');

        $models = [
            AttendanceRecord::class => ['project_id', 'scholarship_holder_id'],
            AttendanceSubmission::class => ['project_id', 'scholarship_holder_id'],
        ];

        foreach ($models as $model => $requiredFields) {
            $instance = new $model;
            $fillable = $instance->getFillable();
            $missing = array_diff($requiredFields, $fillable);

            if (empty($missing)) {
                $this->info('✅ '.class_basename($model).': Todos os campos necessários presentes');
            } else {
                $this->error('❌ '.class_basename($model).': Campos faltando: '.implode(', ', $missing));
            }
        }

        $this->newLine();

        // 5. Verificar DataTables
        $this->info('5️⃣ VERIFICANDO DATATABLES');

        $dataTableClasses = [
            'App\\DataTables\\MyAttendanceRecordDataTable',
            'App\\DataTables\\AttendanceSubmissionDataTable',
        ];

        foreach ($dataTableClasses as $class) {
            if (class_exists($class)) {
                $this->info("✅ DataTable '{$class}' existe");
            } else {
                $this->error("❌ DataTable '{$class}' não encontrado");
            }
        }

        $this->newLine();

        // 6. Verificar views
        $this->info('6️⃣ VERIFICANDO VIEWS');

        $views = [
            'attendance/my',
            'attendance/submissions/my',
            'attendance/partials/project-tabs',
        ];

        foreach ($views as $view) {
            $viewPath = resource_path("views/{$view}.blade.php");
            if (file_exists($viewPath)) {
                $this->info("✅ View '{$view}' existe");
            } else {
                $this->error("❌ View '{$view}' não encontrada");
            }
        }

        $this->newLine();

        // 7. Teste de queries multiprojeto
        $this->info('7️⃣ TESTE DE QUERIES MULTIPROJETO');

        if ($testUser) {
            $holder = $testUser->scholarshipHolder;

            // Contar registros por projeto
            $recordsByProject = AttendanceRecord::query()
                ->where('scholarship_holder_id', $holder->id)
                ->select('project_id', DB::raw('COUNT(*) as total'))
                ->groupBy('project_id')
                ->get();

            $this->line('📊 Registros de frequência por projeto:');
            foreach ($recordsByProject as $record) {
                $project = Project::find($record->project_id);
                $projectName = $project ? $project->name : 'N/A';
                $this->line("   - Projeto {$projectName}: {$record->total} registros");
            }

            // Contar submissões por projeto
            $submissionsByProject = AttendanceSubmission::query()
                ->where('scholarship_holder_id', $holder->id)
                ->select('project_id', DB::raw('COUNT(*) as total'))
                ->groupBy('project_id')
                ->get();

            $this->line('📊 Submissões por projeto:');
            foreach ($submissionsByProject as $submission) {
                $project = Project::find($submission->project_id);
                $projectName = $project ? $project->name : 'N/A';
                $this->line("   - Projeto {$projectName}: {$submission->total} submissões");
            }
        }

        $this->newLine();
        $this->info('🎯 VALIDAÇÃO CONCLUÍDA');
        $this->line('====================');
        $this->info('Verifique os resultados acima e corrija eventuais problemas identificados.');

        return self::SUCCESS;
    }
}
