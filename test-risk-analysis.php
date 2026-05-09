#!/usr/bin/env php
<?php

/**
 * Test Script for Academic Risk Service
 * Runs: php test-risk-analysis.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\AcademicRiskService;

$serv = app(AcademicRiskService::class);

echo "\n╔════════════════════════════════════════════════════════════╗\n";
echo "║     🔍 ANÁLISE DE RISCO ACADÊMICO - TESTE DO SISTEMA      ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

// Análise Global
echo "📊 ANÁLISE GLOBAL (Todos os Alunos)\n";
echo str_repeat('─', 60)."\n";

$allData = $serv->analyzeAll();
echo 'Total de alunos analisados: '.$allData->count()."\n";
echo '  🚨 Críticos (>25% faltas):   '.$allData->where('level', 'critical')->count()." aluno(s)\n";
echo '  ⚠️  Em Risco (15-25%):       '.$allData->where('level', 'risk')->count()." aluno(s)\n";
echo '  ℹ️  Atenção (10-15%):        '.$allData->where('level', 'warning')->count()." aluno(s)\n";
echo '  ✓  OK (<10%):               '.$allData->where('level', 'ok')->count()." aluno(s)\n\n";

// Top 5 Alunos em Risco
echo "⭐ TOP 5 ALUNOS EM RISCO (Por % de Faltas)\n";
echo str_repeat('─', 60)."\n";

$topRisk = $allData->sortByDesc('percent')->take(5);
foreach ($topRisk as $idx => $student) {
    $level = match ($student['level']) {
        'critical' => '🚨',
        'risk' => '⚠️',
        'warning' => 'ℹ️',
        default => '✓'
    };
    echo sprintf("%d. %s %-40s | %6.1f%% (%d/%d)\n",
        $idx + 1,
        $level,
        substr($student['student_name'] ?? "ID: {$student['student_id']}", 0, 38),
        $student['percent'],
        $student['absences'],
        $student['total']
    );
}

// Análise por Turma
echo "\n\n📚 TURMAS CRÍTICAS (Ranking)\n";
echo str_repeat('─', 60)."\n";

$criticalClasses = $serv->getCriticalClassesRanking()->take(5);
foreach ($criticalClasses as $idx => $class) {
    $riskLevel = match ($class['risk_level']) {
        'critical' => '🚨 CRÍTICA',
        'risk' => '⚠️ RISCO',
        'warning' => 'ℹ️ ATENÇÃO',
        default => '✓ OK'
    };

    if ($class['critical_percent'] > 10) {
        echo sprintf("%d. %-30s | %s | %d de %d alunos (%5.1f%%)\n",
            $idx + 1,
            substr($class['name'], 0, 28),
            $riskLevel,
            $class['critical_count'],
            $class['total_students'],
            $class['critical_percent']
        );
    }
}

// Teste de Detecção de Evasão
echo "\n\n🔮 TESTE DE DETECÇÃO DE EVASÃO\n";
echo str_repeat('─', 60)."\n";

$firstCriticalStudent = $allData->where('level', 'critical')->first();
if ($firstCriticalStudent) {
    $churnRisk = $serv->detectChurnRisk($firstCriticalStudent['student_id']);

    echo 'Aluno: '.$firstCriticalStudent['student_name']."\n";
    echo 'Faltas: '.$firstCriticalStudent['percent']."%\n";
    echo 'Risco de Evasão: '.($churnRisk['risk'] ? 'SIM' : 'NÃO')."\n";

    if ($churnRisk['risk']) {
        echo 'Severidade: '.strtoupper($churnRisk['severity'])."\n";
        echo "Motivos:\n";
        foreach ($churnRisk['reasons'] as $reason) {
            echo '  • '.$reason."\n";
        }
    }
} else {
    echo "Nenhum aluno em nível crítico para testar evasão.\n";
}

echo "\n╔════════════════════════════════════════════════════════════╗\n";
echo "║                    ✅ TESTE CONCLUÍDO                      ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";
