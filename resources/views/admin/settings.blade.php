<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>System Settings</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<div class="min-h-screen">
    <div class="max-w-6xl mx-auto px-6 py-10">
        <div class="mb-8 flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">System Settings</h1>
                <p class="text-sm text-gray-500 mt-1">Control organization-wide attendance, security, and company defaults.</p>
            </div>

            <a href="{{ route('admin.dashboard') }}" class="rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                Back to Dashboard
            </a>
        </div>

        <x-alert />

        @if ($errors->any())
            <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-red-700">
                <ul class="list-disc pl-5 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

            <section class="rounded-xl bg-white p-6 shadow">
                <h2 class="text-lg font-semibold text-gray-800">Company Details</h2>
                <div class="mt-5 grid gap-5 md:grid-cols-2">
                    <label class="block">
                        <span class="text-sm font-semibold text-gray-700">Company Name</span>
                        <input name="company_name" value="{{ old('company_name', $settings->company_name) }}" required
                               class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none">
                    </label>

                    <label class="block">
                        <span class="text-sm font-semibold text-gray-700">Company Address</span>
                        <input name="company_address" value="{{ old('company_address', $settings->company_address) }}"
                               class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none">
                    </label>
                </div>
            </section>

            <section class="rounded-xl bg-white p-6 shadow">
                <h2 class="text-lg font-semibold text-gray-800">Attendance Rules</h2>
                <div class="mt-5 grid gap-5 md:grid-cols-3">
                    <label class="block">
                        <span class="text-sm font-semibold text-gray-700">Work Start Time</span>
                        <input type="time" name="work_start_time" value="{{ old('work_start_time', \Illuminate\Support\Str::of($settings->work_start_time)->substr(0, 5)) }}" required
                               class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none">
                    </label>

                    <label class="block">
                        <span class="text-sm font-semibold text-gray-700">Work End Time</span>
                        <input type="time" name="work_end_time" value="{{ old('work_end_time', \Illuminate\Support\Str::of($settings->work_end_time)->substr(0, 5)) }}" required
                               class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none">
                    </label>

                    <label class="block">
                        <span class="text-sm font-semibold text-gray-700">Late Grace Minutes</span>
                        <input type="number" name="late_grace_minutes" value="{{ old('late_grace_minutes', $settings->late_grace_minutes) }}" min="0" max="240" required
                               class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none">
                    </label>
                </div>

                <div class="mt-5 grid gap-5 md:grid-cols-2">
                    <label class="block">
                        <span class="text-sm font-semibold text-gray-700">Allowed Networks</span>
                        <textarea name="allowed_networks" rows="5"
                                  class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none"
                                  placeholder="127.0.0.1&#10;192.168.1.0/24">{{ old('allowed_networks', $settings->allowed_networks) }}</textarea>
                    </label>

                    <div class="rounded-lg border border-gray-200 p-4">
                        <label class="flex items-center gap-3">
                            <input type="hidden" name="late_evidence_required" value="0">
                            <input type="checkbox" name="late_evidence_required" value="1" @checked(old('late_evidence_required', $settings->late_evidence_required))
                                   class="h-5 w-5 rounded border-gray-300 text-blue-600">
                            <span class="text-sm font-semibold text-gray-700">Require evidence for late check-in</span>
                        </label>
                    </div>
                </div>
            </section>

            <section class="rounded-xl bg-white p-6 shadow">
                <h2 class="text-lg font-semibold text-gray-800">Policy Defaults</h2>
                <div class="mt-5 grid gap-5 md:grid-cols-2">
                    <label class="block">
                        <span class="text-sm font-semibold text-gray-700">Password Expiry Days</span>
                        <input type="number" name="password_expiry_days" value="{{ old('password_expiry_days', $settings->password_expiry_days) }}" min="30" max="365" required
                               class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none">
                    </label>

                    <label class="block">
                        <span class="text-sm font-semibold text-gray-700">Default Annual Leave Days</span>
                        <input type="number" name="default_annual_leave_days" value="{{ old('default_annual_leave_days', $settings->default_annual_leave_days) }}" min="0" max="60" required
                               class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none">
                    </label>
                </div>
            </section>

            <div class="flex justify-end">
                <button class="rounded-lg bg-blue-600 px-6 py-2.5 font-semibold text-white shadow hover:bg-blue-700">
                    Save Settings
                </button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
