<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-[#48bb78] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#38a169] focus:bg-[#38a169] active:bg-[#2f855a] focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
