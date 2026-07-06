<x-auth-layout>

    <div class="p-6 mt-10 bg-white border shadow-sm sm:mx-auto sm:w-full sm:max-w-sm rounded-xl border-zinc-100">

        <div class="mb-6 text-center">
            <img class="mx-auto mb-6 rounded-lg shadow-sm h-14 w-14"
                src="{{ Vite::asset('resources/images/logo.svg') }}" alt="Your Company">
            <h5 class="font-semibold text-zinc-900 ">Register an account</h5>
        </div>

        <form action="/register" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="name" class="block font-medium text-sm/6 text-zinc-900 ">Name</label>
                <div class="mt-2">
                    <input id="name" type="text" name="name" value="{{ old('name') }}"
                        class="block w-full rounded-md bg-white  px-3 py-1.5 text-base text-zinc-900  outline-1 -outline-offset-1 outline-zinc-200  shadow-xs placeholder:text-zinc-400 focus:outline-2 focus:-outline-offset-2 focus:outline-blue-600 sm:text-sm/6" />
                    @error('name')
                    <p class="mt-2 text-sm text-red-500 "><span class="font-medium">
                            {{ $message }}
                    </p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="email" class="block font-medium text-sm/6 text-zinc-900 ">Email
                    address</label>
                <div class="mt-2">
                    <input id="email" type="email" name="email" autocomplete="email" value="{{ old('email') }}"
                        class="block w-full rounded-md bg-white  px-3 py-1.5 text-base text-zinc-900  outline-1 -outline-offset-1 outline-zinc-200  shadow-xs placeholder:text-zinc-400 focus:outline-2 focus:-outline-offset-2 focus:outline-blue-600 sm:text-sm/6" />
                    @error('email')
                    <p class="mt-2 text-sm text-red-500 "><span class="font-medium">
                            {{ $message }}
                    </p>
                    @enderror
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between">
                    <label for="password" class="block font-medium text-sm/6 text-zinc-900 ">Password</label>
                </div>
                <div class="mt-2">
                    <input id="password" type="password" name="password" autocomplete="current-password"
                        class="block w-full rounded-md bg-white  px-3 py-1.5 text-base text-zinc-900  outline-1 -outline-offset-1 outline-zinc-200  shadow-xs placeholder:text-zinc-400 focus:outline-2 focus:-outline-offset-2 focus:outline-blue-600 sm:text-sm/6" />
                    @error('password')
                    <p class="mt-2 text-sm text-red-500 "><span class="font-medium">
                            {{ $message }}
                    </p>
                    @enderror
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between">
                    <label for="confirmPassword" class="block font-medium text-sm/6 text-zinc-900 ">Confirm
                        Password</label>
                </div>
                <div class="mt-2">
                    <input id="confirmPassword" type="password" name="password_confirmation"
                        autocomplete="current-password"
                        class="block w-full rounded-md bg-white  px-3 py-1.5 text-base text-zinc-900  outline-1 -outline-offset-1 outline-zinc-200  shadow-xs placeholder:text-zinc-400 focus:outline-2 focus:-outline-offset-2 focus:outline-blue-600 sm:text-sm/6" />
                    @error('password_confirmation')
                    <p class="mt-2 text-sm text-red-500 "><span class="font-medium">
                            {{ $message }}
                    </p>
                    @enderror
                </div>
            </div>

            <div>
                <button type="submit"
                    class="flex w-full justify-center rounded-md mb-3 bg-blue-600 px-3 py-1.5 text-sm/6 font-semibold cursor-pointer text-white shadow-xs hover:bg-blue-500 transition-colors duration-200 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">Register</button>
                <a href="/login"
                    class="flex w-full justify-center rounded-md px-3 py-1.5 text-sm/6 font-semibold hover:bg-zinc-100  text-zinc-900  transition-colors duration-200">
                    Cancel
                </a>
            </div>
        </form>

        <p class="mt-8 text-center text-sm/6 text-zinc-500">
            Already have an account?
            <a href="/login" class="font-medium text-blue-600 transition-colors duration-200 hover:text-blue-500">Sign
                in</a>
        </p>
    </div>

</x-auth-layout>