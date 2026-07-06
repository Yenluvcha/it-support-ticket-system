<x-auth-layout>

    <div class="p-6 mt-10 bg-white border shadow-sm sm:mx-auto sm:w-full sm:max-w-sm rounded-xl border-zinc-100">

        <div class="mb-6 text-center">
            <img class="mx-auto mb-6 rounded-lg shadow-sm h-14 w-14"
                src="{{ Vite::asset('resources/images/logo.svg') }}" alt="Your Company">
            <h5 class="font-semibold text-zinc-900">Sign in to your account</h5>
        </div>

        <form action="#" method="POST" class="space-y-6">
            @csrf
            <div>
                <label for="email" class="block font-medium text-sm/6 text-zinc-900">Email
                    address</label>
                <div class="mt-2">
                    <input id="email" type="email" name="email" autocomplete="email" value="{{ old('email') }}" required
                        class="block w-full rounded-md bg-white  px-3 py-1.5 text-base text-zinc-900  outline-1 -outline-offset-1 outline-zinc-200 shadow-xs placeholder:text-zinc-400 focus:outline-2 focus:-outline-offset-2 focus:outline-blue-600 sm:text-sm/6" />
                    @error('email')
                    <p class="mt-2 text-sm text-red-500 "><span class="font-medium">
                            {{ $message }}
                    </p>
                    @enderror
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between">
                    <label for="password" class="block font-medium text-sm/6 text-zinc-900">Password</label>
                    {{-- <div class="text-sm">
                        <a href="#" class="font-medium text-blue-600 hover:text-blue-500">Forgot password?</a>
                    </div> --}}
                </div>
                <div class="mt-2">
                    <input id="password" type="password" name="password" autocomplete="current-password" required
                        class="block w-full rounded-md bg-white  px-3 py-1.5 text-base text-zinc-900  outline-1 -outline-offset-1 outline-zinc-200  shadow-xs placeholder:text-zinc-400 focus:outline-2 focus:-outline-offset-2 focus:outline-blue-600 sm:text-sm/6" />
                    @error('password')
                    <p class="mt-2 text-sm text-red-500 "><span class="font-medium">
                            {{ $message }}
                    </p>
                    @enderror
                </div>
            </div>

            <div>
                <button type="submit"
                    class="flex w-full justify-center rounded-md bg-blue-600 px-3 py-1.5 text-sm/6 font-semibold cursor-pointer text-white shadow-xs hover:bg-blue-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 transition-colors duration-200">Sign
                    in</button>
            </div>
        </form>

        <p class="mt-8 text-center text-sm/6 text-zinc-500">
            Don't have an account?
            <a href="/register"
                class="font-medium text-blue-600 transition-colors duration-200 hover:text-blue-500">Create account</a>
        </p>
    </div>

</x-auth-layout>