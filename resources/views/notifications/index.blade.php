@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Mes notifications') }}</h5>
                    @if($unreadCount > 0)
                        <form action="{{ route('notifications.markAllAsRead') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                {{ __('Marquer tout comme lu') }}
                            </button>
                        </form>
                    @endif
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @forelse ($notifications as $notification)
                        <div class="notification-item mb-3 p-3 border-bottom {{ $notification->read_at ? '' : 'bg-light' }}">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="notification-content">
                                        <strong>{{ $notification->data['title'] ?? 'Notification' }}</strong>
                                        <p class="mb-1">{{ $notification->data['message'] }}</p>
                                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                    </div>
                                    @if (isset($notification->data['link']))
                                        <a href="{{ $notification->data['link'] }}" class="btn btn-sm btn-link px-0">
                                            {{ __('Voir détails') }}
                                        </a>
                                    @endif
                                </div>
                                @if(!$notification->read_at)
                                    <form action="{{ route('notifications.markAsRead', $notification->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-sm btn-outline-secondary">
                                            {{ __('Marquer comme lu') }}
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="fas fa-bell-slash fa-3x text-muted"></i>
                            </div>
                            <h5>{{ __('Aucune notification') }}</h5>
                            <p class="text-muted">{{ __('Vous n\'avez pas de notifications pour le moment.') }}</p>
                        </div>
                    @endforelse

                    <div class="d-flex justify-content-center mt-4">
                        {{ $notifications->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection