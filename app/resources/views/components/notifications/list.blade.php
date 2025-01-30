<div id="carouselExampleControls" class="container carousel slide mb-5" data-bs-ride="carousel">
    <div class="d-flex gap-2 align-items-center mb-3 justify-content-between">
        <h3 class="m-0">Уведомления</h3>
        <div class="d-flex gap-2 lign-items-center">
            <span class="badge bg-danger">Критичные</span>
            <span class="badge bg-primary">Информирование</span>
            <span class="badge bg-dark">Системные уведомления</span>
        </div>
    </div>
    <div class="carousel-inner">
        @foreach (auth()->user()->notifications()->orderBy('created_at', 'desc')->orderBy('read_at', 'asc')->get()->chunk(4) as $i => $notifications)
            <div class="carousel-item {{ $i == 0 ? 'active' : null }} px-5 {{ empty($notification->read_at) == false ? 'opacity-75' : null }}"
                data-bs-interval="60000">
                <div class="row d-flex align-items-stretch">
                    @foreach ($notifications as $notification)
                        @php $type = 'primary'; @endphp
                        <div class="col-md-4 col-xl-3">
                            <button
                                class="w-100 card bg-c-blue order-card p-3 {{ \App\Enums\NotificationBootstrapColor::find($notification->data['type'])->bootstrapme() }} text-white pb-5 h-100 d-block"
                                data-id="{{ $notification->id }}">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <h6 class="m-0">{{ $notification->data['title'] }}</h6>
                                    <p class="m-0">
                                        <small>
                                            {{ \Carbon\Carbon::parse($notification->data['time'])->format('d.m') }}
                                        </small>
                                    </p>
                                </div>
                                <small class="m-0">{{ $notification->data['message'] }}</small>
                                @if (empty($notification->read_at))
                                    <span
                                        class="position-absolute bottom-0 start-50 translate-middle badge rounded-pill bg-success">
                                        Новое
                                        <span class="visually-hidden">unread messages</span>
                                    </span>
                                @endif
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
    <button class="carousel-control-prev w-auto m-auto d-inline-block opacity-100" type="button"
        data-bs-target="#carouselExampleControls" data-bs-slide="prev" style="height: fit-content">
        <span class="carousel-control-prev-icon bg-primary rounded" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next w-auto m-auto d-inline-block opacity-100" type="button"
        data-bs-target="#carouselExampleControls" data-bs-slide="next" style="height: fit-content">
        <span class="carousel-control-next-icon bg-primary rounded" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>
