@component($typeForm, get_defined_vars())
    <button
            data-controller="button"
            data-turbo="{{ var_export($turbo) }}"
            @empty(!$confirm)
                data-action="button#confirm"
                data-button-confirm="{{ $confirm }}"
            @endempty
            @foreach (@$data ?: [] as $key => $value)
                data-{{ $key }}="{!! $value !!}"
            @endforeach
        {{ $attributes }}>

        @isset($icon)
            <x-orchid-icon :path="$icon" class="{{ empty($name) ?: 'me-2'}}"/>
        @endisset

        <span>{{ $name ?? '' }}</span>
    </button>
@endcomponent
