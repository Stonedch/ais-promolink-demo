@if (@$errors->any())
    <div class="alert alert-danger w-25 opacity-75 position-fixed bottom-0 start-50 translate-middle-x" role="alert">
        <h4 class="alert-heading">Ошибка!</h4>
        <ul class="m-0">{!! implode('', $errors->all('<li>:message</li>')) !!}</ul>
        <button
            class="close-button position-absolute top-0 start-100 translate-middle badge border border-light rounded-circle bg-danger p-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg"
                viewBox="0 0 16 16">
                <path
                    d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z" />
            </svg>
        </button>
    </div>
@endif
