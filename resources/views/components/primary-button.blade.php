<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn btn-primary btn-md rounded-xl uppercase tracking-widest text-xs font-bold']) }}>
    {{ $slot }}
</button>
