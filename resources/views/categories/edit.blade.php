<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Modifica Categoria') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-6">
                <form method="POST" action="{{ route('categories.update', $category) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="name" :value="__('Nome Categoria')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $category->name)" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="color" :value="__('Colore')" />
                        <div class="mt-1 flex items-center gap-3">
                            <input type="color" id="color" name="color" value="{{ old('color', $category->color ?? '#6366f1') }}" class="h-10 w-14 p-1 rounded border border-gray-300 cursor-pointer" required>
                            <span class="text-sm text-gray-500">Scegli un colore per la categoria</span>
                        </div>
                        <x-input-error :messages="$errors->get('color')" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('Aggiorna Categoria') }}</x-primary-button>
                        <a href="{{ route('categories.index') }}" class="text-gray-600 hover:text-gray-900">Annulla</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
