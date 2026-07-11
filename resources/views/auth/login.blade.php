<x-auth-layout>

    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
        <div class="shadow-sm card bg-base-200 rounded-xl border border-base-300">
            <div class="p-6 card-body">

                <div class="mb-6 text-center">
                    <img class="mx-auto mb-6 rounded-lg shadow-sm h-14 w-14"
                        src="{{ Vite::asset('resources/images/logo.svg') }}" alt="Your Company">
                    <h3 class="text-lg font-semibold">Sign in to your account</h3>
                </div>

                <form action="#" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="email" class="mb-3 label">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}"
                            class="w-full input input-bordered" required />
                        @error('email')
                            <p class="mt-2 text-sm text-red-500 "><span class="font-medium">
                                    {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="mb-3 label">Password</label>
                        <input type="password" name="password" id="password" class="w-full input input-bordered"
                            required />
                    </div>

                    <button type="submit" class="w-full mt-4 btn btn-primary">
                        Sign in
                    </button>

                    <p class="mt-4 text-sm text-center">
                        Don't have an account?
                        <a href="/register"
                            class="font-medium text-blue-600 transition-colors duration-200 hover:text-blue-500">Create
                            account</a>
                    </p>
                </form>
            </div>
        </div>
    </div>

</x-auth-layout>
