<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Mensile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="p-4 bg-green-100 text-green-800 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Filters -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <form method="GET" action="{{ route('dashboard') }}" class="flex gap-4 items-end flex-wrap">
                    <div>
                        <x-input-label for="month" :value="__('Mese')" />
                        <select id="month" name="month" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1">
                            @for($i=1; $i<=12; $i++)
                                <option value="{{ $i }}" @selected($i == $month)>{{ DateTime::createFromFormat('!m', $i)->format('F') }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <x-input-label for="year" :value="__('Anno')" />
                        <select id="year" name="year" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1">
                            @for($i=\Carbon\Carbon::now()->year + 2; $i>=2020; $i--)
                                <option value="{{ $i }}" @selected($i == $year)>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <x-primary-button>{{ __('Filtra') }}</x-primary-button>
                    </div>
                </form>
            </div>

            <!-- Stats & Charts -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Total -->
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg flex flex-col justify-center items-center">
                    <h3 class="text-lg font-medium text-gray-900">Totale Mensile</h3>
                    <p class="mt-4 text-4xl font-bold text-indigo-600">€ {{ number_format($total, 2, ',', '.') }}</p>
                </div>
                
                <!-- Pie Chart -->
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg md:col-span-2 flex justify-center h-64">
                    @if($expensesByCategory->count() > 0)
                        <canvas id="categoryPieChart"></canvas>
                    @else
                        <p class="flex items-center text-gray-400">Nessun dato per il grafico</p>
                    @endif
                </div>
            </div>

            <!-- Bar Chart -->
            @if($expensesByCategory->count() > 0)
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg h-80">
                <canvas id="categoryBarChart"></canvas>
            </div>
            @endif

            <!-- Table -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-medium text-gray-900">Elenco Spese</h3>
                    <div class="flex gap-4">
                        <a href="{{ route('export.csv', ['month' => $month, 'year' => $year]) }}" class="px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">Esporta CSV</a>
                        <a href="{{ route('expenses.create') }}" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">Inserisci Spesa</a>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th class="px-6 py-3">Data</th>
                                <th class="px-6 py-3">Categoria</th>
                                <th class="px-6 py-3">Titolo</th>
                                <th class="px-6 py-3">Importo</th>
                                <th class="px-6 py-3">Note</th>
                                <th class="px-6 py-3">Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($expenses as $expense)
                                <tr class="bg-white border-b hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $expense->expense_date->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4">{{ $expense->category->name }}</td>
                                    <td class="px-6 py-4 font-medium text-gray-900">{{ $expense->title }}</td>
                                    <td class="px-6 py-4 font-bold whitespace-nowrap">€ {{ number_format($expense->amount, 2, ',', '.') }}</td>
                                    <td class="px-6 py-4 truncate max-w-xs" title="{{ $expense->notes }}">{{ $expense->notes }}</td>
                                    <td class="px-6 py-4 flex gap-3">
                                        <a href="{{ route('expenses.edit', $expense) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">Modifica</a>
                                        <form action="{{ route('expenses.destroy', $expense) }}" method="POST" onsubmit="return confirm('Sei sicuro di voler eliminare questa spesa?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 font-medium">Elimina</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-6 py-8 text-center text-gray-400">Nessuna spesa trovata per questo mese. Clicca su "Inserisci Spesa" per cominciare!</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Scripts -->
    @if($expensesByCategory->count() > 0)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chartData = @json($expensesByCategory);
            const labels = Object.keys(chartData);
            const data = Object.values(chartData);
            
            const backgroundColors = [
                '#6366f1', '#ec4899', '#f59e0b', '#10b981', '#3b82f6', '#8b5cf6',
                '#ef4444', '#14b8a6', '#f97316', '#06b6d4', '#d946ef', '#84cc16'
            ];

            const pieCtx = document.getElementById('categoryPieChart');
            if (pieCtx) {
                new Chart(pieCtx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: data,
                            backgroundColor: backgroundColors,
                            borderWidth: 0,
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'right' }
                        }
                    }
                });
            }

            const barCtx = document.getElementById('categoryBarChart');
            if (barCtx) {
                new Chart(barCtx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Totale Speso (€)',
                            data: data,
                            backgroundColor: '#6366f1',
                            borderRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: { beginAtZero: true, grid: { borderDash: [2, 4] } },
                            x: { grid: { display: false } }
                        }
                    }
                });
            }
        });
    </script>
    @endif
</x-app-layout>
