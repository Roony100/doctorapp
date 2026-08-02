<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">Add Doctor</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                <form method="POST" action="{{ route('admin.doctors.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label>Full name</label>
                        <input type="text" name="name" value="{{ old('name') }}" class="border rounded w-full p-2" required>
                    </div>
                    <div>
                        <label>Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="border rounded w-full p-2" required>
                    </div>
                    <div>
                        <label>Password</label>
                        <input type="password" name="password" class="border rounded w-full p-2" required>
                    </div>
                    <div>
                        <label>Specialty</label>
                        <select name="specialty_id" class="border rounded w-full p-2" required>
                            <option value="">Select...</option>
                            @foreach ($specialties as $specialty)
                                <option value="{{ $specialty->id }}">{{ $specialty->libelle }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label>Numero d'ordre</label>
                        <input type="text" name="numero_ordre" value="{{ old('numero_ordre') }}" class="border rounded w-full p-2" required>
                    </div>
                    <div>
                        <label>Consultation duration (minutes)</label>
                        <input type="number" name="duree_consultation" value="{{ old('duree_consultation', 30) }}" class="border rounded w-full p-2" required>
                    </div>
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Create Doctor Account</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>