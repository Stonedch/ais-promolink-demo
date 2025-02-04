<div class="container">
    <div id="carouselExampleControls" class="carousel slide mb-5" data-bs-ride="carousel">
        <div class="d-flex gap-2 align-items-center mb-3 justify-content-between">
            <h3 class="m-0">Уведомления</h3>
            <div class="d-flex gap-2 lign-items-center">
                @foreach (\App\Enums\NotificationBootstrapColor::cases() as $case)
                    <span class="badge {{ $case->bootstrapme() }} {{ $case->bootstrapmeColor() }}">Критичные</span>
                @endforeach
            </div>
        </div>
        <div class="carousel-inner">
            @foreach (auth()->user()->notifications()->orderBy('created_at', 'desc')->orderBy('read_at', 'asc')->get()->chunk(4) as $i => $notifications)
                <div class="carousel-item {{ $i == 0 ? 'active' : null }} px-5 {{ empty($notification->read_at) == false ? 'opacity-75' : null }}"
                    data-bs-interval="60000">
                    <div class="row d-flex align-items-stretch">
                        @foreach ($notifications as $notification)
                            @php
                                $type = 'primary';
                                $status = \App\Enums\NotificationBootstrapColor::find($notification->data['type']);
                            @endphp

                            <div class="col-md-4 col-xl-3">
                                <button
                                    class="notification-card w-100 card bg-c-blue order-card p-3 {{ $status->bootstrapme() }} text-white pb-5 h-100 d-block"
                                    data-id="{{ $notification->id }}" data-title="{{ $notification->data['title'] }}"
                                    data-message="{{ $notification->data['message'] }}">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <h6 class="m-0 {{ $status->bootstrapmeColor() }}">
                                            {{ $notification->data['title'] }}</h6>
                                        <p class="m-0">
                                            <small class="{{ $status->bootstrapmeColor() }}">
                                                {{ \Carbon\Carbon::parse($notification->data['time'])->format('d.m') }}
                                            </small>
                                        </p>
                                    </div>
                                    <p class="m-0 fs-10 lh-sm {{ $status->bootstrapmeColor() }}">
                                        {{ $notification->data['message'] }}
                                    </p>
                                    @if (empty($notification->read_at))
                                        <span
                                            class="notification-new position-absolute bottom-0 start-50 translate-middle badge rounded-pill bg-success">
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
</div>
