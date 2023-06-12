<template>
    <div class="w-1/2 h-52 bg-[#D9D9D9] rounded-xl pb-2 py-5 grid grid-cols-3 relative">
        <div class="my-3 col-start-2 col-span-2 h-full">
            <h3 class="text-center text-gray-700">
                <slot name="title"></slot>
                <slot name="icon"></slot>
            </h3>
        </div>
        <div class="mb-28 px-2 overflow-y-scroll h-4/6 w-full col-start-2 col-span-2">
                <p class="text-sm font-bold">
                    <slot name="subtitle"></slot>
                </p>
        </div>
        <div class="px-2 h-4/6 w-full absolute top-12 left-5">
            <slot name="image"></slot>
        </div>
    </div>
</template>