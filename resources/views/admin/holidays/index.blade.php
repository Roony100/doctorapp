<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">Clinic Holidays</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="bg-red-100 text-red-800 p-3 rounded">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                <h3 class="font-semibold mb-4">Add a clinic holiday</h3>
                <form method="POST" action="{{ route('admin.holidays.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label>Start date</label>
                        <input type="date" name="date_debut" required class="border rounded w-full p-2">
                    </div>
                    <div>
                        <label>End date</label>
                        <input type="date" name="date_fin" required class="border rounded w-full p-2">
                    </div>
                    <div>
                        <label>Label</label>
                        <input type="text" name="libelle" placeholder="e.g. Christmas break" required class="border rounded w-full p-2">
                    </div>
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Add Holiday</button>
                </form>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                <h3 class="font-semibold mb-4">Upcoming and past holidays</h3>
                @forelse ($holidays as $holiday)
                    <div class="flex justify-between items-center border-b py-2">
                        <span>{{ $holiday->date_debut }} to {{ $holiday->date_fin }} - {{ $holiday->libelle }}</span>
                        <form method="POST" action="{{ route('admin.holidays.destroy', $holiday) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600">Remove</button>
                        </form>
                    </div>
                @empty
                    <p>No clinic holidays set.</p>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>