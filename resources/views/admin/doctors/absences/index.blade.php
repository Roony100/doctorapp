<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">My Absences</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-100 text-red-800 rounded">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 mb-6">
                <h3 class="font-semibold mb-4">Declare an absence</h3>
                <form method="POST" action="{{ route('doctor.absences.store') }}" class="space-y-4">
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
                        <label>Reason (optional)</label>
                        <input type="text" name="motif" class="border rounded w-full p-2">
                    </div>
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Add</button>
                </form>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                <h3 class="font-semibold mb-4">Upcoming absences</h3>
                @forelse ($absences as $absence)
                    <div class="flex justify-between items-center border-b py-2">
                        <span>{{ $absence->date_debut }} to {{ $absence->date_fin }} - {{ $absence->motif }}</span>
                        <form method="POST" action="{{ route('doctor.absences.destroy', $absence->id) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600">Remove</button>
                        </form>
                    </div>
                @empty
                    <p>No absences declared.</p>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>