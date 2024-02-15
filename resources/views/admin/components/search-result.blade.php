<x-base.transition
    class="search-result absolute right-0 z-10 mt-[3px] hidden"
    selector=".show"
    enter="transition-all ease-linear duration-150"
    enterFrom="mt-5 invisible opacity-0 translate-y-1"
    enterTo="mt-[3px] visible opacity-100 translate-y-0"
    leave="transition-all ease-linear duration-150"
    leaveFrom="mt-[3px] visible opacity-100 translate-y-0"
    leaveTo="mt-5 invisible opacity-0 translate-y-1"
>
    <div class="box w-[450px] p-5">
        <div class="mb-2 font-medium">Pages</div>
        <div class="mb-5">
            <a
                class="flex items-center"
                href=""
            >
                <div
                    class="flex h-8 w-8 items-center justify-center rounded-full bg-success/20 text-success dark:bg-success/10">
                    <x-base.lucide
                        class="h-4 w-4"
                        icon="Inbox"
                    />
                </div>
                <div class="ml-3">Mail Settings</div>
            </a>
            <a
                class="mt-2 flex items-center"
                href=""
            >
                <div
                    class="flex h-8 w-8 items-center justify-center rounded-full bg-pending/10 text-pending">
                    <x-base.lucide
                        class="h-4 w-4"
                        icon="Users"
                    />
                </div>
                <div class="ml-3">Users & Permissions</div>
            </a>
            <a
                class="mt-2 flex items-center"
                href=""
            >
                <div
                    class="flex h-8 w-8 items-center justify-center rounded-full bg-primary/10 text-primary/80 dark:bg-primary/20">
                    <x-base.lucide
                        class="h-4 w-4"
                        icon="CreditCard"
                    />
                </div>
                <div class="ml-3">Transactions Report</div>
            </a>
        </div>
        <div class="mb-2 font-medium">Users</div>
        <div class="mb-5">
            @foreach (array_slice($fakers, 0, 4) as $faker)
                <a
                    class="mt-2 flex items-center"
                    href=""
                >
                    <div class="image-fit h-8 w-8">
                        <img
                            class="rounded-full"
                            src="/images/no-image.jpg"
                            alt="Midone Tailwind HTML Admin Template"
                        />
                    </div>
                    <div class="ml-3">{{ $faker['users'][0]['name'] }}</div>
                    <div class="ml-auto w-48 truncate text-right text-xs text-slate-500">
                        {{ $faker['users'][0]['email'] }}
                    </div>
                </a>
            @endforeach
        </div>
        <div class="mb-2 font-medium">Products</div>
        @foreach (array_slice($fakers, 0, 4) as $faker)
            <a
                class="mt-2 flex items-center"
                href=""
            >
                <div class="image-fit h-8 w-8">
                    <img
                        class="rounded-full"
                        src="/images/no-image.jpg"
                        alt="Midone Tailwind HTML Admin Template"
                    />
                </div>
                <div class="ml-3">{{ $faker['products'][0]['name'] }}</div>
                <div class="ml-auto w-48 truncate text-right text-xs text-slate-500">
                    {{ $faker['products'][0]['category'] }}
                </div>
            </a>
        @endforeach
    </div>
</x-base.transition>
