<button {{ $attributes->merge(['type' => 'button', 'class' => 'btn btn-secondary btn-md rounded-xl uppercase tracking-widest text-xs font-bold shadow-sm']) }}>
    {{ $slot }}
</button>
