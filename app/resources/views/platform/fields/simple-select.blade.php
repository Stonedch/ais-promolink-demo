<select {{ $attributes }} data-value="{{ $value }}">
    @foreach ($options as $key => $option)
        <option value="{{ $key }}"
            @isset($value)
                    @if (is_array($value) && in_array($key, $value)) selected
                    @elseif (isset($value[$key]) && $value[$key] == $option) selected
                    @elseif ($key == $value) selected
                    @endif
                @endisset>
            {{ $option }}</option>
    @endforeach
</select>
