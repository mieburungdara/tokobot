@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">Telegram Bot Details</div>

                <div class="card-body">
                    <div class="form-group">
                        <label for="name">Bot Name:</label>
                        <p>{{ $telegramBot->name }}</p>
                    </div>
                    <div class="form-group">
                        <label for="token">Bot Token:</label>
                        <p>{{ $telegramBot->token }}</p>
                    </div>
                    <div class="form-group">
                        <label for="username">Bot Username:</label>
                        <p>{{ $telegramBot->username }}</p>
                    </div>
                    <div class="form-group">
                        <label for="is_active">Is Active:</label>
                        <p>{{ $telegramBot->is_active ? 'Yes' : 'No' }}</p>
                    </div>
                    <a href="{{ route('admin.telegram_bots.index') }}" class="btn btn-primary">Back to List</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection