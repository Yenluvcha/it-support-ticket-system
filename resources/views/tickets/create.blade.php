<x-app-layout title="Create ticket">
    <div class="mx-auto max-w-3xl">
        <div class="mb-6">
            <a href="{{ route('tickets.index') }}" class="link link-hover text-sm">&larr; My Tickets</a>
            <h1 class="mt-3 text-2xl font-bold">Create a support ticket</h1>
            <p class="mt-1 text-base-content/70">Describe the issue and IT support will review it.</p>
        </div>

        <form action="{{ route('tickets.store') }}" method="POST" class="card border border-base-300 bg-base-100 shadow-sm">
            @csrf
            <div class="card-body">
                <x-validation-errors />

                <label class="form-control">
                    <span class="label-text mb-2 font-medium">Subject</span>
                    <input type="text" name="subject" value="{{ old('subject') }}" maxlength="255" required
                        class="input input-bordered w-full" placeholder="e.g. Unable to access email" />
                </label>

                <label class="form-control mt-4">
                    <span class="label-text mb-2 font-medium">Description</span>
                    <textarea name="description" rows="8" maxlength="5000" required class="textarea textarea-bordered w-full"
                        placeholder="Tell us what happened, when it started, and any error messages.">{{ old('description') }}</textarea>
                </label>

                <div class="card-actions mt-6 justify-end">
                    <a href="{{ route('tickets.index') }}" class="btn btn-ghost">Cancel</a>
                    <button type="submit" class="btn btn-primary">Submit ticket</button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
