<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">
            {{ __('My Working Hours') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 mb-6">
                <h3 class="font-semibold mb-4">Add a working slot</h3>
                <form method="POST" action="{{ route('doctor.availabilities.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label>Day of week (1 = Monday ... 7 = Sunday)</label>
                        <input type="number" name="jour_semaine" min="1" max="7" required class="border rounded w-full p-2">
                    </div>
                    <div>
                        <label>Start time</label>
                        <input type="time" name="heure_debut" required class="border rounded w-full p-2">
                    </div>
                    <div>
                        <label>End time</label>
                        <input type="time" name="heure_fin" required class="border rounded w-full p-2">
                    </div>
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Add</button>
                </form>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                <h3 class="font-semibold mb-4">Current working hours</h3>
                @forelse ($availabilities as $availability)
                    <div class="flex justify-between items-center border-b py-2">
                        <span>Day {{ $availability->jour_semaine }}: {{ $availability->heure_debut }} - {{ $availability->heure_fin }}</span>
                        <form method="POST" action="{{ route('doctor.availabilities.destroy', $availability->id) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600">Remove</button>
                        </form>
                    </div>
                @empty
                    <p>No working hours set yet.</p>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>