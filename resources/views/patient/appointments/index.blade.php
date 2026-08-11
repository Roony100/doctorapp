<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('My Appointments') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                    {{ session('status') }}
                </div>
            @endif

            @php
                $statusStyles = [
                    'en_attente' => 'bg-yellow-100 text-yellow-800',
                    'confirme' => 'bg-green-100 text-green-800',
                    'termine' => 'bg-gray-200 text-gray-700',
                    'annule' => 'bg-red-100 text-red-800',
                    'absent' => 'bg-red-100 text-red-800',
                ];
                $statusLabels = [
                    'en_attente' => 'Pending',
                    'confirme' => 'Confirmed',
                    'termine' => 'Completed',
                    'annule' => 'Cancelled',
                    'absent' => 'Absent',
                ];
            @endphp

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

                <!-- List column: about 1/4 width on large screens -->
                <div class="lg:col-span-1 space-y-6 lg:max-h-[80vh] lg:overflow-y-auto">

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-4 text-gray-900 dark:text-gray-100">
                            <h3 class="font-semibold mb-3">{{ __('Upcoming') }}</h3>

                            @forelse ($upcoming as $appointment)
                                <div class="border-b py-2 text-sm">
                                    <p class="font-semibold">Dr. {{ $appointment->doctor->user->name }}</p>
                                    <p class="text-gray-500">{{ $appointment->doctor->specialty->libelle }}</p>
                                    <p class="text-gray-500">
                                        {{ \Carbon\Carbon::parse($appointment->date_heure_debut)->format('D, M j - H:i') }}
                                    </p>
                                    <span class="text-xs px-2 py-1 rounded {{ $statusStyles[$appointment->statut] ?? 'bg-gray-100 text-gray-700' }}">
                                        {{ $statusLabels[$appointment->statut] ?? $appointment->statut }}
                                    </span>

                                    @if (in_array($appointment->statut, ['en_attente', 'confirme']))
                                        <div class="mt-2 flex gap-2">
                                            <a href="{{ route('patient.booking.reschedule', ['doctor' => $appointment->doctor_id, 'appointment' => $appointment]) }}"
                                            class="text-xs bg-indigo-600 text-white px-2 py-1 rounded">
                                                {{ __('Reschedule') }}
                                            </a>
                                            <form method="POST" action="{{ route('patient.appointments.cancel', $appointment) }}"
                                                onsubmit="return confirm('Cancel this appointment?')">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-xs bg-red-600 text-white px-2 py-1 rounded">
                                                    {{ __('Cancel') }}
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <p class="text-sm">{{ __('No upcoming appointments.') }}</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-4 text-gray-900 dark:text-gray-100">
                            <h3 class="font-semibold mb-3">{{ __('Past') }}</h3>

                            @forelse ($past as $appointment)
                                <div class="border-b py-2 text-sm">
                                    <p class="font-semibold">Dr. {{ $appointment->doctor->user->name }}</p>
                                    <p class="text-gray-500">{{ $appointment->doctor->specialty->libelle }}</p>
                                    <p class="text-gray-500">
                                        {{ \Carbon\Carbon::parse($appointment->date_heure_debut)->format('D, M j - H:i') }}
                                    </p>
                                    <span class="text-xs px-2 py-1 rounded {{ $statusStyles[$appointment->statut] ?? 'bg-gray-100 text-gray-700' }}">
                                        {{ $statusLabels[$appointment->statut] ?? $appointment->statut }}
                                    </span>
                                </div>
                            @empty
                                <p class="text-sm">{{ __('No past appointments.') }}</p>
                            @endforelse
                        </div>
                    </div>

                </div>

                <!-- Calendar column: about 3/4 width on large screens -->
                <div class="lg:col-span-3">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">

                        <div class="flex justify-between items-center mb-4">
                            <a href="{{ route('patient.appointments.index', ['month' => $previousMonth]) }}" class="text-indigo-600">
                                &larr; {{ __('Previous') }}
                            </a>
                            <h3 class="font-semibold text-lg text-gray-900 dark:text-gray-100">
                                {{ $monthStart->format('F Y') }}
                            </h3>
                            <a href="{{ route('patient.appointments.index', ['month' => $nextMonth]) }}" class="text-indigo-600">
                                {{ __('Next') }} &rarr;
                            </a>
                        </div>

                        <div class="grid grid-cols-7 gap-1 text-center text-xs font-semibold text-gray-500 mb-2">
                            <div>Mon</div>
                            <div>Tue</div>
                            <div>Wed</div>
                            <div>Thu</div>
                            <div>Fri</div>
                            <div>Sat</div>
                            <div>Sun</div>
                        </div>

                        <div class="grid grid-cols-7 gap-1">
                            @for ($i = 0; $i < $startOffset; $i++)
                                <div></div>
                            @endfor

                            @for ($day = 1; $day <= $daysInMonth; $day++)
                                @php
                                    $dateKey = $monthStart->copy()->day($day)->format('Y-m-d');
                                    $dayAppointments = $calendarAppointments->get($dateKey, collect());
                                @endphp
                                <div class="border rounded p-1 min-h-[80px] text-xs align-top">
                                    <div class="font-semibold text-gray-700 dark:text-gray-300">{{ $day }}</div>
                                    @foreach ($dayAppointments as $appointment)
                                        <div class="mt-1 p-1 bg-indigo-100 text-indigo-800 rounded truncate">
                                            {{ \Carbon\Carbon::parse($appointment->date_heure_debut)->format('H:i') }}
                                            Dr. {{ $appointment->doctor->user->name }}
                                        </div>
                                    @endforeach
                                </div>
                            @endfor
                        </div>

                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>