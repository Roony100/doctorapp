<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">Edit Doctor</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                <form method="POST" action="{{ route('admin.doctors.update', $doctor) }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label>Full name</label>
                        <input type="text" name="name" value="{{ old('name', $doctor->user->name) }}" class="border rounded w-full p-2" required>
                    </div>
                    <div>
                        <label>Specialty</label>
                        <select name="specialty_id" class="border rounded w-full p-2" required>
                            @foreach ($specialties as $specialty)
                                <option value="{{ $specialty->id }}" @selected($specialty->id === $doctor->specialty_id)>
                                    {{ $specialty->libelle }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label>Numero d'ordre</label>
                        <input type="text" name="numero_ordre" value="{{ old('numero_ordre', $doctor->numero_ordre) }}" class="border rounded w-full p-2" required>
                    </div>
                    <div>
                        <label>Consultation duration (minutes)</label>
                        <input type="number" name="duree_consultation" value="{{ old('duree_consultation', $doctor->duree_consultation) }}" class="border rounded w-full p-2" required>
                    </div>
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Update</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>