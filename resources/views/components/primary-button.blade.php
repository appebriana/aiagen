<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn btn-primary btn-md shadow-lg shadow-primary-500/30']) }}>
    {{ $slot }}
</button>
