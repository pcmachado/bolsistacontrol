{{-- resources/views/dashboard.blade.php (Proposta) --}}
@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="space-y-8">
        {{-- Cabeçalho de Boas-Vindas --}}
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Bem-vindo(a) de volta, {{ Auth::user()->name }}!</h1>
            <p class="mt-2 text-lg text-gray-600">Aqui está um resumo da sua atividade.</p>
        </div>

        {{-- Cards de Resumo --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex items-center">
                    <div class="bg-blue-500 text-white p-3 rounded-full">
                        <i class="fas fa-calendar-check fa-lg"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-500 text-sm font-medium uppercase">Frequências no Mês</p>
                        <p class="text-2xl font-bold text-gray-800">12 / 20</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex items-center">
                    <div class="bg-green-500 text-white p-3 rounded-full">
                        <i class="fas fa-clock fa-lg"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-500 text-sm font-medium uppercase">Horas Cumpridas (Mês)</p>
                        <p class="text-2xl font-bold text-gray-800">48h / 80h</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex items-center">
                    <div class="bg-yellow-500 text-white p-3 rounded-full">
                        <i class="fas fa-bell fa-lg"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-500 text-sm font-medium uppercase">Notificações</p>
                        <p class="text-2xl font-bold text-gray-800">3</p>
                    </div>
                </div>
            </div>

        </div>

        {{-- Área de Ações Rápidas --}}
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-bold text-gray-700 mb-4">Ações Rápidas</h2>
            <div class="flex space-x-4">
                <a href="#" class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition duration-300">
                    Registrar Frequência de Hoje
                </a>
                <a href="#" class="px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition duration-300">
                    Ver Meu Histórico
                </a>
            </div>
        </div>
    </div>
@endsection