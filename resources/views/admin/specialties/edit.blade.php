<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">Edit Specialty</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                <form method="POST" action="{{ route('admin.specialties.update', $specialty) }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label>Name</label>
                        <input type="text" name="libelle" value="{{ old('libelle', $specialty->libelle) }}" class="border rounded w-full p-2" required>
                    </div>
                    <div>
                        <label>Description</label>
                        <textarea name="description" class="border rounded w-full p-2">{{ old('description', $specialty->description) }}</textarea>
                    </div>
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Update</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>