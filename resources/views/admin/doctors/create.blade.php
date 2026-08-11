<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">Add Doctor</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                <form method="POST" action="{{ route('admin.doctors.store') }}" class="space-y-4">
                    @csrf
                    @if ($errors->any())
    <div class="bg-red-100 text-red-800 p-3 rounded">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
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
    <label>Specialties</label>
    <div class="space-y-1 border rounded p-2">
        @foreach ($specialties as $specialty)
            <label class="flex items-center gap-2">
                <input type="checkbox" name="specialty_ids[]" value="{{ $specialty->id }}">
                {{ $specialty->libelle }}
            </label>
        @endforeach
    </div>
</div>
                    <div>
    <label>Numero d'ordre</label>
    <input type="text" value="{{ $nextNumeroOrdre }}" class="border rounded w-full p-2 bg-gray-100" disabled>
    <p class="text-sm text-gray-500 mt-1">This is generated automatically.</p>
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