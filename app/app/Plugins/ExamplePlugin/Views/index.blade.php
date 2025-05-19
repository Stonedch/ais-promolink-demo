@extends('ExamplePlugin::layout')

@section('content')
    <a href="{{ route('web.example-plugin.index') }}">/ExamplePlugin</a>

    <h1>This's just example view.</h1>
    <p>Please close this page!</p>

    <h2>Config:</h2>
    <p>config('plugins.ExamplePlugin.enabled'): {{ config('plugins.ExamplePlugin.enabled') }}</p>

    <h2>Migrations:</h2>
    <table>
        <tr>
            <th>id</th>
            <th>created_at</th>
            <th>updated_at</th>
        </tr>
        @foreach ($examples as $example)
            <tr>
                <td>{{ $example->id }}</td>
                <td>{{ $example->created_at }}</td>
                <td>{{ $example->updated_at }}</td>
            </tr>
        @endforeach
    </table>
@endsection
