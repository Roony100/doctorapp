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
        <p class="font-semibold">{{ $appointment->patient->user->name }}</p>
        <p class="text-gray-500">
            {{ \Carbon\Carbon::parse($appointment->date_heure_debut)->format('D, M j - H:i') }}
        </p>
        <span class="text-xs px-2 py-1 rounded {{ $statusStyles[$appointment->statut] ?? 'bg-gray-100 text-gray-700' }}">
            {{ $statusLabels[$appointment->statut] ?? $appointment->statut }}
        </span>

        @if ($appointment->statut === 'en_attente')
            <div class="flex gap-2 mt-2">
                <form method="POST" action="{{ route('doctor.appointments.confirm', $appointment) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="text-xs bg-green-600 text-white px-2 py-1 rounded">
                        {{ __('Confirm') }}
                    </button>
                </form>
                <form method="POST" action="{{ route('doctor.appointments.cancel', $appointment) }}"
                      onsubmit="return confirm('Cancel this appointment?')">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="text-xs bg-red-600 text-white px-2 py-1 rounded">
                        {{ __('Refuse') }}
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
        <p class="font-semibold">{{ $appointment->patient->user->name }}</p>
        <p class="text-gray-500">
            {{ \Carbon\Carbon::parse($appointment->date_heure_debut)->format('D, M j - H:i') }}
        </p>
        <span class="text-xs px-2 py-1 rounded {{ $statusStyles[$appointment->statut] ?? 'bg-gray-100 text-gray-700' }}">
            {{ $statusLabels[$appointment->statut] ?? $appointment->statut }}
        </span>

        @if ($appointment->statut === 'confirme')
            <div class="flex gap-2 mt-2">
                <form method="POST" action="{{ route('doctor.appointments.complete', $appointment) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="text-xs bg-green-600 text-white px-2 py-1 rounded">
                        {{ __('Mark Done') }}
                    </button>
                </form>
                <form method="POST" action="{{ route('doctor.appointments.mark-absent', $appointment) }}"
                      onsubmit="return confirm('Mark patient as absent?')">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="text-xs bg-orange-600 text-white px-2 py-1 rounded">
                        {{ __('Mark Absent') }}
                    </button>
                </form>
            </div>
        @endif
        @if ($appointment->statut === 'termine')
            <div class="mt-2">
                <a href="{{ route('doctor.consultation.edit', $appointment) }}" class="text-xs text-indigo-600 underline">
                    {{ $appointment->consultation ? __('View/Edit Notes') : __('Add Notes') }}
                </a>
            </div>
        @endif
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
                            <a href="{{ route('doctor.appointments.index', ['month' => $previousMonth]) }}" class="text-indigo-600">
                                &larr; {{ __('Previous') }}
                            </a>
                            <h3 class="font-semibold text-lg text-gray-900 dark:text-gray-100">
                                {{ $monthStart->format('F Y') }}
                            </h3>
                            <a href="{{ route('doctor.appointments.index', ['month' => $nextMonth]) }}" class="text-indigo-600">
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
                                    $offDayType = $offDays[$dateKey] ?? null;
                                    $cellClass = $offDayType ? 'bg-red-50 border-red-200' : '';
                                @endphp
                                <div class="border rounded p-1 min-h-[80px] text-xs align-top {{ $cellClass }}">
                                    <div class="font-semibold text-gray-700 dark:text-gray-300">{{ $day }}</div>
                                    @if ($offDayType)
                                        <div class="text-red-700 text-xs">
                                            {{ $offDayType === 'holiday' ? __('Holiday') : __('Absent') }}
                                        </div>
                                    @endif
                                    @foreach ($dayAppointments as $appointment)
                                        <div class="mt-1 p-1 bg-indigo-100 text-indigo-800 rounded truncate">
                                            {{ \Carbon\Carbon::parse($appointment->date_heure_debut)->format('H:i') }}
                                            {{ $appointment->patient->user->name }}
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