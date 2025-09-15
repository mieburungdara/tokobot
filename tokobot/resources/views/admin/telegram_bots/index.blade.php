@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Telegram Bots</div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <a href="{{ route('admin.telegram_bots.create') }}" class="btn btn-primary mb-3">Add New Bot</a>

                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Username</th>
                                <th>Active</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bots as $bot)
                                <tr>
                                    <td>{{ $bot->id }}</td>
                                    <td>{{ $bot->name }}</td>
                                    <td>{{ $bot->username }}</td>
                                    <td>{{ $bot->is_active ? 'Yes' : 'No' }}</td>
                                    <td>
                                        <a href="{{ route('admin.telegram_bots.show', $bot->id) }}" class="btn btn-info btn-sm">View</a>
                                        <a href="{{ route('admin.telegram_bots.edit', $bot->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                        <form action="{{ route('admin.telegram_bots.destroy', $bot->id) }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection