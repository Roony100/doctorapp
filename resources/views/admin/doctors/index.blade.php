<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">Doctors</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
            @endif

            <a href="{{ route('admin.doctors.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded inline-block mb-4">Add Doctor</a>

            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                @forelse ($doctors as $doctor)
                    <div class="flex justify-between items-center border-b py-2">
                    <div>
                    <strong>{{ $doctor->user->name }}</strong> ({{ $doctor->user->email }})<br>
                    <span class="text-sm text-gray-500">{{ $doctor->specialties->pluck('libelle')->join(', ') }} - {{ $doctor->numero_ordre }}</span>
                    </div>
                    <div class="flex gap-2">
                    <a href="{{ route('admin.doctors.edit', $doctor) }}" class="text-blue-600">Edit</a>
                    <form method="POST" action="{{ route('admin.doctors.destroy', $doctor) }}" onsubmit="return confirm('Delete this doctor?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600">Delete</button>
                        </form>
                        </div>
                        </div>
                        @empty
                    <p>No doctors yet.</p>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>